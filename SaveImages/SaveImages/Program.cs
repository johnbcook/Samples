using System;
using System.Xml;
using System.Xml.Linq;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using WebClientLibrary;
using CsvParseLibrary;
using Utilities;


namespace SaveImages
{

    
    class Program
    {
       // Set file directory and target image directory
       static public string FullFilePath { get { return System.Configuration.ConfigurationManager.AppSettings["FullFilePath"]; } }
       static public string NewFilePath { get { return System.Configuration.ConfigurationManager.AppSettings["NewFilePath"]; } }
       static public string NutrientPath { get { return System.Configuration.ConfigurationManager.AppSettings["NutrientPath"]; } }
       static public string NewNutPath { get { return System.Configuration.ConfigurationManager.AppSettings["NewNutPath"]; } }
        static void Main(string[] args)
        {

           // try
           // {
                WebClientUtility WCU = new WebClientUtility();
                CsvLibrary cl = new CsvLibrary();
                XmlUtil xu = new XmlUtil();
                NutritionLabel nl = new NutritionLabel();

                XDocument testx =  XDocument.Load(@"D:\TestData\samplexml.xml");

            

                //Console.WriteLine("Target CSV File: " + FullFilePath);
                //Console.WriteLine("Target Image Directory: " + NewFilePath);

                //WCU.ReadAndParseCSV(FullFilePath, NewFilePath);
                //WCU.SaveURLImages();

                //Console.WriteLine("Images Processed, press Enter to continue.");
               // Console.WriteLine("This is Nut Path: " + NutrientPath);
                //cl.ReadCsv(NutrientPath);
               // cl.WriteCsv(NutrientPath, NewNutPath);


                XmlDocument test = new XmlDocument();

                test.Load(@"D:\TestData\samplexml.xml");
                                 
                //Console.WriteLine(xu.GetNodeCount(test, "//product"));

                XmlNodeList products = XmlUtil.GetNodeList(test, "//product");

            //@"D:\TestData\uploads.csv"
                List<NutrientUpload> uber = nl.GenerateNutrientList(products);

               
                //foreach (XmlNode x in products)
                //{
                   
                //    Console.WriteLine(x.ChildNodes.Count);
                //    foreach(XmlNode y in x.ChildNodes)
                //    {
                //        Console.Write(y.Name);

                //        if (y.Name.Equals("STOCK_CODE"))
                //        {
                //            Console.WriteLine(y.InnerText);
                //        }

                //    }
                //   // Console.WriteLine(x.SelectSingleNode("//STOCK_CODE").InnerText);
                //   // Console.WriteLine(x.FirstChild.InnerText);
                //}   

                //List<NutrientUpload> uber = new List<NutrientUpload>();

                //uber.Add(new NutrientUpload { StockCode = "Sylvester", HTML = "Test" });
                //uber.Add(new NutrientUpload { StockCode = "Jack", HTML = "Test" });

                //foreach (NutrientUpload x in uber)
                //{
                //    Console.WriteLine(x.StockCode + " " + x.HTML);
                //}

                nl.GenerateNutritionCsv(nl.GenerateNutrientList(products), @"D:\TestData\nutrientupload.csv");

                Console.Read();
          //  } catch(Exception ex)
          //  {
            //    Console.WriteLine("An error has occurred.  Contact developer with the exception: " + ex.Message);
            //    Console.Read();

          //  }

        }
    }
}
