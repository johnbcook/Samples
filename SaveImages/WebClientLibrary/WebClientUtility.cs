using System;
using System.IO;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Net;
using Utilities;
using LumenWorks.Framework.IO.Csv;
using CsvParseLibrary;

//using CsvHelper;
//using CsvHelper.Configuration;

namespace WebClientLibrary
{
    public class WebClientUtility
    {

        FileUtil fu = new FileUtil();
        string[] filecontents;
        Dictionary<String, String> imagefiles = new Dictionary<String, String>();
        public List<ProductImage> productimages = new List<ProductImage>();
        public List<SkippedRecord> skippedrecords = new List<SkippedRecord>();
        static int counter = 0;

        
        public void SaveURLImages()
        {

           WebClient client = new WebClient();
           foreach (KeyValuePair<String, String> kvp in imagefiles)
           {
              
               client.DownloadFile(kvp.Value, kvp.Key);
           }

           client.Dispose();
        }

    /// <summary>
     /// Method reads a CSV file and sets up DataDictionary
     /// </summary>
     /// <param name="filepath">Full File Path of csv file</param>
     /// <param name="newfilepath">Target directory for images</param>  
    public Dictionary<String, String> GenerateImageFiles(string filepath, string newfilepath)
   {

  
     string newimage;

    // open the file "data.csv" which is a CSV file with headers
         using (CsvReader csv = new CsvReader(new StreamReader(filepath), true))
         {
             int fieldCount = csv.FieldCount;

             string[] headers = csv.GetFieldHeaders();
             
             List<String> newheaders = headers.ToList();

             int imageurl = newheaders.FindIndex(item => item.Equals("ImageURL"));
             int product = newheaders.FindIndex(item => item.Equals("productcode"));
             int stockcode = newheaders.FindIndex(item => item.Equals("StockCode"));
             int upc = newheaders.FindIndex(item => item.Equals("UPC"));

           while (csv.ReadNextRecord())
             {
                 for (int i = 0; i < fieldCount; i++)
                 {
                     
                     if (i == imageurl)
                     {
                         //Console.WriteLine(string.Format("{0} = {1};",
                         //                headers[i], csv[i]));
                         if (String.IsNullOrEmpty(csv[i]))
                         {
                             Console.WriteLine("No image found, skipping");
                             //Write Error File
                             NewSkippedRecord(csv[stockcode], csv[upc], csv[product], csv[imageurl], "No Image URL Found");
                             continue;
                         }
                         if (csv[product].Contains("N/A"))
                         {
                             Console.WriteLine("Product not uploaded, skipping");
                             NewSkippedRecord(csv[stockcode], csv[upc], csv[product], csv[imageurl], "No Image URL Found");
                             continue;
                         }


                         newimage = ImageName(csv[i], newfilepath);
                        // Console.WriteLine("FileName: " + newimage);
                         imagefiles.Add(newimage, csv[i]);
                        // Console.WriteLine("Skipped Records: " + skippedrecords.Count());
                         // Generate ProductImage list
                         string[] parsepath = newimage.Split('\\');
                         NewProductImage(csv[product], parsepath[parsepath.Count() -1]);
                        // Console.WriteLine("Products: " + productimages.Count());

                     }
                 }
             }
       
        Console.WriteLine("Total Images Processed: " + imagefiles.Count);
        return imagefiles;
    }
}

    /// <summary>
    /// Method to split url and concatenate file name. 
    /// </summary>
    /// <param name="imageurl">Image URL</param>
    /// <param name="filepath">New directory path</param>
    /// <returns></returns> 
    public string ImageName(string imageurl, string filepath)
       {
           string[] urlparse;
           urlparse = imageurl.Split('/');
           if (urlparse[urlparse.Count() - 1].Contains("no_image"))
           {
               counter++;
               return filepath + "\\" + counter + urlparse[urlparse.Count() - 1];
            
           }

           return filepath + "\\" + urlparse[urlparse.Count() - 1];

       }

    /// <summary>
        ///  Method to Generate a new ProductImage object and add the ProductImage to productimages list
        /// </summary>
        /// <param name="productcode"></param>
        /// <param name="imagename"></param>
    public void NewProductImage(string productcode, string imagename)
    {        
        productimages.Add(new ProductImage { productid = productcode, imagename = imagename });

    }
    
    /// <summary>
    /// Method to add a new SkippedRecord object to a list of Skipped Records. 
    /// </summary>
    /// <param name="stockcode"></param>
    /// <param name="upc"></param>
    /// <param name="productcode"></param>
    /// <param name="imageurl"></param>
    /// <param name="reason"></param>
    public void NewSkippedRecord(string stockcode, string upc, string productcode, string imageurl, string reason)
    {
       skippedrecords.Add(new SkippedRecord { stockcode = stockcode, upc = upc, productcode = productcode, imageurl = imageurl, reason = reason });

    }


    }
}
