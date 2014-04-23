using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using CsvHelper;

namespace CsvParseLibrary
{
    public class NutritionInfo
    {

       public string  StockCode {get; set;}           
       public string  UPC {get; set;}	
       public string  NutrientMasterID {get; set;}
       public string  ValuePreparedType {get; set;}
       public string  AddedItem {get; set;}
       public string  Nutrient {get; set;}
       public string  PercentDailyValue {get; set;}
       public string  Quantity {get; set;}
       public string  ServingSizeText {get; set;}
       public string  ServingSizeUOM {get; set;}
       public string  ServingsPerContainer {get; set;}
       public string  UOM {get; set;}
       public string ProprietaryBlends { get; set; }



    }
}
