using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.IO;
using CsvHelper;

namespace CsvParseLibrary
{
    public class CsvLibrary
    {
        string output;

        public void ReadCsv(string path)
        {
            Console.WriteLine("This is Path: " + path);
            //Stream reader will read test.csv file in current folder
            StreamReader sr = new StreamReader(path);
            //Csv reader reads the stream
            CsvReader csvread = new CsvReader(sr);

            //csvread will fetch all record in one go to the IEnumerable object record
            IEnumerable<NutritionInfo> record = csvread.GetRecords<NutritionInfo>();

            foreach (var rec in record) // Each record will be fetched and printed on the screen
            {
               Console.WriteLine(string.Format("Stock Code: {0}, UPC : {1}, NutrientMasterID : {2} <br/>", rec.StockCode, rec.UPC, rec.NutrientMasterID));
            }
            sr.Close();
        }


        public void WriteNutritionCsv(string frompath, string topath)
        {

           
          
            //Stream reader will read test.csv file in current folder
            StreamReader sr = new StreamReader(frompath);
            //For easy understanding we will be writing same csv data read from one test.csv file to another copyfile.csv file
            StreamWriter write = new StreamWriter(topath);
 
            //Csv reader reads the stream
            CsvReader csvread = new CsvReader(sr);
 
            //Csv writer stream
            CsvWriter csw = new CsvWriter(write);
 
            
            //csvread will fetch all record in one go to the IEnumerable object record
            IEnumerable<NutritionInfo> record = csvread.GetRecords<NutritionInfo>();
            csw.WriteHeader<NutritionInfo>();
 
            foreach (var rec in record) // Each record will be fetched and printed on the screen
            {
             //reads csv and print output
            //  Console.WriteLine(string.Format("Stock Code: {0}, UPC : {1}, NutrientMasterID : {2} <br/>", rec.StockCode, rec.UPC, rec.NutrientMasterID));
             //same time writes the csv file to another file : copyfile.csv
              
              csw.WriteRecord<NutritionInfo>(rec);
             }
              sr.Close();
              write.Close();//close file streams
         }

        /// <summary>
        /// Method to generate a .csv file based on a list of NutrientUpload objects
        /// </summary>
        /// <param name="objects">List of NutrtientUploadObjects</param>
        /// <param name="topath">Fully qualifed path to .csv</param>
        public static void WriteNutritionInfoCsv<T>(List<T> objects,  string topath)
        {
            //Console.Write("Object Count: " + objects.Count);      
            //For easy understanding we will be writing same csv data read from one test.csv file to another copyfile.csv file
            using (StreamWriter write = new StreamWriter(topath))
            {
                //Csv writer stream
                CsvWriter csw = new CsvWriter(write);
                csw.WriteHeader<T>();

                foreach (var rec in objects) // Each record will be fetched and printed on the screen
                {
                    //reads csv and print output
                   // Console.WriteLine(string.Format("Stock Code: {0}, HTML : {1}  <br/>", rec.StockCode, rec.HTML));
                    //same time writes the csv file to another file : copyfile.csv
                    csw.WriteRecord<T>(rec);
                }
            }
          
        }

        /// <summary>
        /// Method to generate a .csv file based on a list of objects
        /// </summary>
        /// <param name="objects">List of objects</param>
        /// <param name="topath">Fully qualifed path to .csv</param>
        public static void WriteCsv<T>(List<T> objects, string topath)
        {
            //Console.WriteLine("Product Images Object Count: " + objects.Count);
           // Console.Read();
            //For easy understanding we will be writing same csv data read from one test.csv file to another copyfile.csv file
            using (StreamWriter write = new StreamWriter(topath))
            {
                //Csv writer stream
                CsvWriter csw = new CsvWriter(write);
                csw.WriteHeader<T>();

                foreach (var rec in objects) // Each record will be fetched and printed on the screen
                {
                    //reads csv and print output                    
                    //same time writes the csv file to another file : copyfile.csv
                                      csw.WriteRecord<T>(rec);
                }
            }

        }
        

    }
}
