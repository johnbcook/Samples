using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Xml;
using Microsoft.VisualStudio.TestTools.UnitTesting;

namespace CsvParseLibrary
{
    /// <summary>
    /// Utility class for XML files used to assist Fitnesse fixture developers with creating fixtures.
    /// </summary>
    public class XmlUtil
    {
        /// <summary>
        /// Replaces the content of a tag within the xml. 
        /// </summary>
        /// <param name="tagName">The case sensitive name of the tag whose contents will be replaced.</param>
        /// <param name="contents">The contents to find the tag within and replace.</param>
        /// <param name="replaceWith">The text to replace the tag contents with.</param>
        /// <param name="replaceAll">Must be false.  Throws an exception otherwise.</param>
        /// <returns>The contents with the replacement done.</returns>
        public string ReplaceTagContents(string tagName, string contents, string replaceWith, bool replaceAll)
        {
            if (!string.IsNullOrEmpty(tagName) && !string.IsNullOrEmpty(contents))
            {
                if (replaceAll)
                {
                    throw new NotImplementedException("Replace All Does Not work yet, please supply false");
                }
                //First, find the tag
                string tag = "<" + tagName + ">";
                if (replaceAll)
                {
                }
                else
                {
                    int startIndex = contents.IndexOf(tag);
                    if (startIndex >= 0)
                    {
                        int contentsStartIndex = startIndex + tag.Length;

                        int endContentsIndex = contents.IndexOf("<", contentsStartIndex);
                        contents = contents.Remove(contentsStartIndex, (endContentsIndex - contentsStartIndex));
                        contents = contents.Insert(contentsStartIndex, replaceWith);
                    }
                }
            }
            return contents;
        }

        /// <summary>
        /// Gets the contents of a tag within xml.
        /// </summary>
        /// <param name="tagName">The case sensitive name of the tag.</param>
        /// <param name="contents">The contents to find the tag within</param>
        /// <returns>The contents of the tag</returns>
        public string GetTagContents(string tagName, string contents)
        {
            string tagContents = null;
            if (!string.IsNullOrEmpty(tagName) && !string.IsNullOrEmpty(contents))
            {
                string tag = "<" + tagName + ">";
                int startIndex = contents.IndexOf(tag);
                if (startIndex >= 0)
                {
                    int contentsStartIndex = startIndex + tag.Length;

                    int endContentsIndex = contents.IndexOf("<", contentsStartIndex);

                    tagContents = contents.Substring(startIndex + tag.Length, (endContentsIndex - contentsStartIndex));
                }
            }
            return tagContents;
        }

        /// <summary>
        /// Method to read in a XML file and return an XMLDocument object. 
        /// </summary>
        /// <param name="configpath"></param>
        /// <returns></returns>
        public XmlDocument ReadConfig(string configpath)
        {
            XmlDocument config = new XmlDocument();
            config.Load(configpath);
            Assert.IsNotNull(config);
            return config;
        }

        /// <summary>
        /// Method searches an XML doc for xpath and returns InnerText of XMLNode
        /// </summary>
        /// <param name="xmldoc"></param>
        /// <param name="xpath"></param>
        /// <returns></returns>
        public static string GetXMLNodeInnerText(XmlDocument xmldoc, string xpath)
        {

            XmlNode newnode = xmldoc.SelectSingleNode(xpath);
            string text = newnode.InnerText;
            return text;

        }

        /// <summary>
        /// Method to get XML Node Count
        /// </summary>
        /// <param name="xmldoc">XmlDocument</param>
        /// <param name="xpath">Node Path</param>
        /// <returns></returns>
        public int GetNodeCount(XmlDocument xmldoc, string xpath)
        {
            XmlNodeList nodelist = xmldoc.SelectNodes(xpath);
            return nodelist.Count;
        }

        /// <summary>
        /// Method to get XmlNodeList
        /// </summary>
        /// <param name="xmldoc"></param>
        /// <param name="xpath"></param>
        /// <returns></returns>
        public static XmlNodeList GetNodeList(XmlDocument xmldoc, string xpath)
        {
            XmlNodeList nodelist = xmldoc.SelectNodes(xpath);
            return nodelist;
        }


    }
}
