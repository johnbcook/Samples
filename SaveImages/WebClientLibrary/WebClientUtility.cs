using System;
using System.IO;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Net;
using Utilities;
using LumenWorks.Framework.IO.Csv;
//using CsvHelper;
//using CsvHelper.Configuration;

namespace WebClientLibrary
{
    public class WebClientUtility
    {

        FileUtil fu = new FileUtil();
        string[] filecontents;
        Dictionary<String, String> imagefiles = new Dictionary<String, String>();

        public void SaveURLImages()
        {

           WebClient client = new WebClient();
           foreach (KeyValuePair<String, String> kvp in imagefiles)
           {
               client.DownloadFile(kvp.Key, kvp.Value);
           }

           client.Dispose();
        }

    /// <summary>
     /// Method reads a CSV file and sets up DataDictionary
     /// </summary>
     /// <param name="filepath">Full File Path of csv file</param>
     /// <param name="newfilepath">Target directory for images</param>  
    public Dictionary<String, String> ReadAndParseCSV(string filepath, string newfilepath)
   {

  
     string newimage;

    // open the file "data.csv" which is a CSV file with headers
         using (CsvReader csv = new CsvReader(new StreamReader(filepath), true))
         {
             int fieldCount = csv.FieldCount;

             string[] headers = csv.GetFieldHeaders();

             List<String> newheaders = headers.ToList();

             int result = newheaders.FindIndex(item => item.Equals("ImageURL"));

             Console.WriteLine(result);

             while (csv.ReadNextRecord())
             {
                 for (int i = 0; i < fieldCount; i++)
                 {

                     if (i == result)
                     {
                         //  Console.WriteLine(string.Format("{0} = {1};",
                         //                headers[i], csv[i]));


                         newimage = ImageName(csv[i], newfilepath);
                         imagefiles.Add(csv[i], newimage);
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
           return filepath + "\\" + urlparse[urlparse.Count() - 1];

       }


    }
}
