using System;
using System.Xml;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace CsvParseLibrary
{
    public class NutritionLabel
    {

        string fatgrams = "";
        string fatpercent = "";       
        string css = "<link href=\"my.css\" rel=\"stylesheet\"/>" + " "; 
        string section =  "<section class=\"performance-facts\">" + " ";
        string header = "<header class=\"performance-facts__header\">" + " ";
        string h1 = "<h1 class=\"performance-facts__title\">Nutrition Facts</h1>" + " ";
        string headerend = "</header>";
        string end = "</section>";
        string pftstart = "<table class=\"performance-facts__table\"><thead> <tr> <th colspan=\"3\" class=\"small-info\"> Amount Per Serving</th></tr>";
        string pftstart2 = "</thead><tbody><tr><th colspan=\"2\"><b>Calories</b>";
        string dailyvalue = "<tr class=\"thick-row\"><td colspan=\"3\" class=\"small-info\"><b>% Daily Value*</b></td></tr>";
       
       
        
    
        StringBuilder htmlstring = new StringBuilder();
        
        /// <summary>
        /// Method to generate the Nutrition Label HTML based on a 
        /// list of Nutrient nodes.
        /// </summary>
        /// <param name="nutrients">Nodelist of Nutrient nodes</param>
        /// <returns>string containing formatted Nutrient Label HTML</returns>
        public string GenerateNutritionLabel(XmlNodeList nutrients)
        {

            string nutrientlabel = "";

            return nutrientlabel;
        }

        /// <summary>
        /// Method to generate a list of NutrientUpload objects 
        /// based on a product XML. 
        /// </summary>
        /// <param name="path">path to product xml</param>
        /// <returns>List of NutrientUpload objects</returns>
        public List<NutrientUpload> GenerateNutrientList(XmlNodeList products)
        {
            List<NutrientUpload> nutrientlist = new List<NutrientUpload>();
            List<String> ingredients = new List<String>();
            string stockcode = "";
            string upc = "";
            int i = 0;
            string servingsize = "<p> Serving Size";
            string servingtext = "";
            string servingper = "";
            bool servingfound = false;
            bool servings = false;
            bool servingsper = false;
            bool cexist = false;
            bool cfat = false;
            string tfatstart = "";
            string ingredientname = "";
            string percent = "";
            string total = "";
            List<String> exclusion = GenerateExcludeList();
           
           
            
            Console.WriteLine("Count:" + products.Count);

            // Generate NutrientUpload list
            foreach (XmlNode x in products)
            {
                servingfound = false;
                servings = false;
                servingsper = false;
                string calories = "";
                string calfromfat = "";
                string unit = "";
                bool totalfat = false;
                
                

                //Console.WriteLine(x.ChildNodes.Count);
                //Console.WriteLine(x.ChildNodes.Count);
                foreach (XmlNode y in x.ChildNodes)
                {
                    //Console.Write(y.Name);
                    // Set Stock Code
                    if (y.Name.Equals("STOCK_CODE"))
                    {
                       // Console.WriteLine(y.InnerText);
                        stockcode = y.InnerText;
                    }

                    // Set UPC
                    if (y.Name.Equals("UPC"))
                    {
                        //Console.WriteLine(y.InnerText);
                        upc = y.InnerText;
                    }
                                        
                    if (y.Name.Equals("NUTRIENTS"))
                    {
                        //Get ChildNodes of Nutrients - these are Nutrient nodes
                        XmlNodeList nutrients = y.ChildNodes;
                       // Console.WriteLine("Nutrient Nodes" + nutrients.Count);
                        // Get the Nutrient Meta Nodes
                        foreach (XmlNode z in nutrients)
                        {
                            XmlNodeList nutrientmeta = z.ChildNodes;
                           // Console.WriteLine("Nutrient Meta" + nutrientmeta.Count);

                            // Delve into nutrient meta
                            foreach (XmlNode s in nutrientmeta)
                            {
                                
                                // ServingSizeText
                                if (s.Name.Equals("SERVINGSIZETEXT")&& !String.IsNullOrEmpty(s.InnerText) & !(servingfound))
                                {
                                   servingtext = servingsize + " " + s.InnerText;
                                  // Console.WriteLine("SText:" + s.InnerText);                                  
                                   servingfound = true;                               
                                }

                                // ServingSizeUOM
                                if (s.Name.Equals("SERVINGSIZEUOM") && !String.IsNullOrEmpty(s.InnerText) & !(servings))
                                {
                                    servingtext = servingtext + " " + s.InnerText + "</p>";
                                    //Console.WriteLine("SText:" + s.InnerText);
                                    servings = true;
                                }

                                // ServingSizeUOM
                                if (s.Name.Equals("SERVINGSPERCONTAINER") && !String.IsNullOrEmpty(s.InnerText) & !(servingsper))
                                {
                                    servingper = "<p>" + "Servings Per Container " + s.InnerText + "</p>";
                                  //  Console.WriteLine("SPer:" + "Servings Per Container " + s.InnerText);
                                    servingsper = true;
                                }

                                //// Calories
                                if (!(cexist))
                                  if (s.Name.Equals("NAME") && s.InnerText.Equals("Calories"))
                                    {
                                        // Console.WriteLine("SPer:" + "Servings Per Container " + s.InnerText);
                                        cexist = true;
                                        // Cycle through NextSiblings for Quantity
                                        //s.NextSibling
                                        foreach(XmlNode zz in nutrientmeta)
                                        {                                           
                                            if(zz.Name.Equals("QUANTITY"))
                                            {
                                                string[] newcal = zz.InnerText.Split('.');
                                                calories = " " + newcal[0] + "</th>";
                                               // Console.WriteLine(calories);
                                            }
                                        }
                                    }

                                if (s.Name.Equals("NAME") && s.InnerText.Equals("Calories from Fat"))
                                {

                                    // Cycle through NextSiblings for Quantity
                                    //s.NextSibling
                                    foreach (XmlNode zz in nutrientmeta)
                                    {
                                        if (zz.Name.Equals("QUANTITY"))
                                        {
                                            string[] fatcal = zz.InnerText.Split('.');
                                            calfromfat = "<td><b>Calories from Fat </b>" +fatcal[0]  + "</td></tr>";
                                            //Console.WriteLine(calfromfat);
                                        }
                                    }
                                 }

                                // Total Fat
                                if (s.Name.Equals("NAME") && s.InnerText.Equals("Total Fat"))
                                {
                                    totalfat = true;

                                    // Cycle through NextSiblings for Quantity
                                    //s.NextSibling
                                    foreach (XmlNode zz in nutrientmeta)
                                    {
                                        if (zz.Name.Equals("QUANTITY"))
                                        {
                                            string[] tfat = zz.InnerText.Split('.');
                                            fatgrams = tfat[0];
                                           // Console.WriteLine(fatgrams);
                                        }

                                        if (zz.Name.Equals("PCT") && !String.IsNullOrEmpty(zz.InnerText))
                                        {                                           
                                            fatpercent = zz.InnerText + "%";
                                            //Console.WriteLine(fatpercent);
                                        }

                                        if (zz.Name.Equals("UOM") && !String.IsNullOrEmpty(zz.InnerText))
                                        {
                                            unit = zz.InnerText;
                                           // Console.WriteLine(unit);
                                        }
                                    }

                                 tfatstart = "<tr> <th colspan=\"2\"> <b>Total Fat</b> " + fatgrams + unit +"</th><td><b>" + fatpercent + "</b></td></tr>";
                                }

                                // All others

                                if(s.Name.Equals("NAME") && !exclusion.Contains(s.InnerText))
                                {
                                   // Console.WriteLine("Test of innertext: " + s.InnerText);

                                    ingredientname = s.InnerText;

                                    foreach (XmlNode zz in nutrientmeta)
                                    {
                                        if (zz.Name.Equals("QUANTITY"))
                                        {
                                            string[] inittotal = zz.InnerText.Split('.');
                                            total = inittotal[0];
                                            
                                        }

                                        if (zz.Name.Equals("PCT") && !String.IsNullOrEmpty(zz.InnerText))
                                        {
                                            percent = zz.InnerText + "%";
                                            //Console.WriteLine(fatpercent);
                                        }

                                        if (zz.Name.Equals("UOM") && !String.IsNullOrEmpty(zz.InnerText))
                                        {
                                            unit = zz.InnerText;
                                            // Console.WriteLine(unit);
                                        }
                                    }

                                    ingredients.Add("<tr><td class=\"blank-cell\"></td><th>" + ingredientname + " " + total + unit + "</th><td><b>" + percent + "</b></td></tr>");
                                    Console.WriteLine(ingredients.Count());

                                }

                                
                                }  
                             }
                        }
                    }
                
               
                // PH for HTML Generation

                htmlstring.Append(css);
                htmlstring.Append(section);
               // Header
                htmlstring.Append(header);
                htmlstring.Append(h1);
               // Serving
                htmlstring.Append(servingtext);
                htmlstring.Append(servingper);
               // End Header
                htmlstring.Append(headerend);
               // Performance Facts Table start
                htmlstring.Append(pftstart + pftstart2);
                htmlstring.Append(calories);
                htmlstring.Append(calfromfat);
                htmlstring.Append(dailyvalue);
                htmlstring.Append(tfatstart);
                foreach(String yyz in ingredients)
                {
                    htmlstring.Append(yyz);
                }

                //end
                htmlstring.Append(end);             

                nutrientlist.Add(new NutrientUpload{ StockCode = stockcode, UPC = upc, HTML = htmlstring.ToString()});
                htmlstring.Clear();
                ingredients.Clear();
            }
             
          //string userName = "John Doe";
          //StringBuilder mailBody = new StringBuilder();
          //mailBody.AppendFormat("<h1>Heading Here</h1>");
          //mailBody.AppendFormat("Dear {0}," userName);
          //mailBody.AppendFormat("<br />");
          //mailBody.AppendFormat("<p>First part of the email body goes here</p>");


            return nutrientlist;

        }

        /// <summary>
        /// Method to generate Nutrient Upload csv.
        /// </summary>
        /// <param name="nutrientinfo">List of NutrientUpload objects</param>
        /// <param name="filepath">Fully qualified csv file name</param>
        public void GenerateNutritionCsv(List<NutrientUpload> nutrientlist, string filepath)
        {
            CsvLibrary.WriteNutritionInfoCsv(nutrientlist, filepath);
        }


        /// <summary>
        /// Method to set exclusion list
        /// </summary>
        /// <returns>List of Strings</String></returns>
        public List<String> GenerateExcludeList()
        {

            List<String> exclude = new List<String>();
            exclude.Add("Calories");
            exclude.Add("Calories from Fat");
            exclude.Add("Total Fat");

            return exclude; 

        }
       

    }
}
