using System;
using System.IO;
using System.Diagnostics;
using System.Configuration;
using System.Reflection;
using System.Text.RegularExpressions;
using System.Xml;
using System.Collections.Generic;

namespace Utilities
{
    /// <summary>
    /// Utility class for files, used to assist Fitnesse fixture developers with creating fixtures.  Requires a properties file to be configured (ususally, Runner.exe.config)
    /// to use those properties.
    /// </summary>
    public class FileUtil
    {
        private const string NEWLINE_PATTERN = @"\r\n|\n\r|\n|\r";
        private const string NEWLINE_REPLACEMENT = "\r\n";
        private static bool results;


        /// <summary>
        /// The name of the ReportScript property
        /// </summary>
        private string ReportScript = "ReportScript";
        /// <summary>
        /// The name of the Compare Tool Path property
        /// </summary>
        private string CompareToolPath = "CompareToolPath";
        /// <summary>
        /// The name of the Compare Tool Executable property
        /// </summary>
        private string CompareToolExe = "CompareToolExe";
        /// <summary>
        /// The name of the FitnesseOutputFileDirectory property
        /// </summary>
        private string FitnesseOutputFileDirectory = "FitnesseOutputFileDirectory";
        /// <summary>
        /// The name of the FitnesseHostURL property
        /// </summary>
        private string FitnesseHostURL = "FitnesseHostURL";
        /// <summary>
        /// The name of the FitnesseURLOutputFilePath property
        /// </summary>
        private string FitnesseURLOutputFilePath = "FitnesseURLOutputFilePath";

        /// <summary>
        /// Reads the file at the specified path.  Throws an exception if filePath is null or empty.
        /// </summary>
        /// <param name="filePath">The full path, including file name, of the file to read</param>
        /// <returns>The contents of the file</returns>
        public string ReadFile(string filePath)
        {
            string contents = null;

            if (string.IsNullOrEmpty(filePath))
            {
                throw new ArgumentNullException("The specified filePath is null or empty");
            }

            contents = File.ReadAllText(filePath);
            return contents;
        }

        /// <summary>
        /// Reads the file at the specified path.  Throws an exception if filePath is null or empty.
        /// </summary>
        /// <param name="filePath">The full path, including file name, of the file to read</param>
        /// <returns>The contents of the file in an array of strings</returns>
        public string[] ReadFileByLine(string filePath)
        {
            string[] contents = null;

            if (string.IsNullOrEmpty(filePath))
            {
                throw new ArgumentNullException("The specified filePath is null or empty");
            }

            contents = File.ReadAllLines(filePath);
            return contents;
        }

        /// <summary>
        /// Creates a File. Throws an exception if filePath or contents are null or empty
        /// </summary>
        /// <param name="filePath">The full path, including file name, of the file to write</param>
        /// <param name="contents">The contents to write to the file</param>
        public static void WriteFile(string filePath, string contents)
        {
            if (string.IsNullOrEmpty(filePath))
            {
                throw new ArgumentNullException("The specified filePath is null or empty");
            }

            if (string.IsNullOrEmpty(contents))
            {
                throw new ArgumentNullException("The contents were null or empty");
            }

            File.WriteAllText(filePath, contents);
        }

        /// <summary>
        /// Method to Write a file line by line.    Use File.ReadAllLines to get an array of lines
        /// </summary>
        /// <param name="filepath"></param>
        /// <param name="contents"></param>
        public void WriteFileByLine(string filePath, string[] contents)
        {

            if (string.IsNullOrEmpty(filePath))
            {
                throw new ArgumentNullException("The specified filePath is null or empty");
            }

            if (contents == null)
            {
                throw new ArgumentNullException("The contents were null or empty");
            }

            File.WriteAllLines(filePath, contents);

        }

        /// <summary>
        /// Creates a directory if the directory doesnt exist.  Throws an exception if path is null or empty
        /// </summary>
        /// <param name="path">The directory to create</param>
        public void CreateDirectoryIfDoesntExist(String path)
        {
            if (string.IsNullOrEmpty(path))
            {
                throw new ArgumentNullException("The specified path is null or empty");
            }
            if (!Directory.Exists(path))
            {
                Directory.CreateDirectory(path);
            }
        }

    
        /// <summary>
        /// Determines if the specified contents and the contents of the specified file are equal. Throws an exception if any of the parameters are null.
        /// </summary>
        /// <param name="contents">The contents to compare</param>
        /// <param name="filePath">The full file path, including filename, to compare its contents with.</param>
        /// <returns>true if they are equal, false otherwise</returns>
        public Boolean IsStringAndFileContentsEqual(string contents, string filePath)
        {
            if (string.IsNullOrEmpty(contents))
            {
                throw new ArgumentNullException("The contents is null or empty");
            }
            if (string.IsNullOrEmpty(filePath))
            {
                throw new ArgumentNullException("The filePath is null or empty");
            }
            return contents.Equals(ReadFile(filePath));
        }

        /// <summary>
        /// Method to delete a file.
        /// </summary>
        /// <param name="filepath">path of file</param>
        /// <returns>true if file has been removed. </returns>
        public static bool DeleteFile(string filepath)
        {
            bool result; 
            if(File.Exists(filepath))
            {
                File.Delete(filepath);
            }

            if (File.Exists(filepath))
            {
                result = false;
            }
            else { result = true; };
            return result; 
            
           

        }

        /// <summary>
        /// Creates an A HREF Tag.
        /// </summary>
        /// <param name="url">The url to create the tag from.</param>
        /// <param name="name">The name of the tag.</param>
        /// <returns>The a href tag</returns>
        public string CreateAHrefTag(String url, String name)
        {
            if (string.IsNullOrEmpty(url))
            {
                throw new ArgumentNullException("The url is null or empty");
            }
            if (string.IsNullOrEmpty(name))
            {
                throw new ArgumentNullException("The name is null or empty");
            }
            return "<a href=\"" + url + "\" target=_BLANK>" + name + "</a>";
        }

        /// <summary>
        /// Creates an output file with the specified contents, based on the path of the existing output file path.  Appends temp to the output path and will place the file there.
        /// Throws an exception if any of the parameters are null or empty.
        /// </summary>
        /// <param name="existingOutputFilePath">The full path, including the filename, of the output file.</param>
        /// <param name="contents">The contents to write to the file.</param>
        /// <returns>The full path, including the filename, of the newly created file.</returns>
        public string CreateOutputFileFromExistingOutputFile(string existingOutputFilePath, string contents)
        {
            if (string.IsNullOrEmpty(existingOutputFilePath))
            {
                throw new ArgumentNullException("The existingOutputFilePath were null or empty");
            }
            if (string.IsNullOrEmpty(contents))
            {
                throw new ArgumentNullException("The ncontentsame were null or empty");
            }

            //Get Path
            string path = existingOutputFilePath.Substring(0, existingOutputFilePath.LastIndexOf("\\"));

            path = path + "\\temp";

            CreateDirectoryIfDoesntExist(path);

            string extension = existingOutputFilePath.Substring(existingOutputFilePath.LastIndexOf("."), existingOutputFilePath.Length - existingOutputFilePath.LastIndexOf("."));

            string newPath = path + "\\" + "out-" + DateTime.Now.Ticks + extension;

            WriteFile(newPath, contents);
            return newPath;
        }


        /// <summary>
        /// Method that will copy file from one directory to another.  If file exists in target directory it will be
        /// deleted prior to the copy
        /// </summary>
        /// <param name="fromfile">path of file to put (includes file name)</param>
        /// <param name="targetfile">path of target file (includes file name)</param>
        /// <returns></returns>
        public bool CopyFile(string fromfile, string targetfile)
        {
            if (System.IO.File.Exists(fromfile))
            {
                if (System.IO.File.Exists(targetfile))
                {
                    System.IO.File.Delete(targetfile);
                }
                System.IO.File.Copy(fromfile, targetfile);
                
            }

            // Verify File Copied
            if (System.IO.File.Exists(targetfile))
            {
                results = true;
            }
            else
            {
                results = false;
            }

            return results;
        }

        /// <summary>
        /// Method to Delete all files in a specified directory
        /// Returns true if all files have been deleted. 
        /// </summary>
        /// <param name="path"></param>
        /// <returns></returns>
        public bool DirectoryDeleteAllFiles(string path)
        {
            Directory.GetFiles(path);
            string[] files = Directory.GetFiles(path);

            foreach (string x in files)
            {
                if (File.Exists(x))
                {
                    File.Delete(x);
                }
            }

            files = Directory.GetFiles(path);

            if (files.Length == 0)
            {
                return true;
            }
            else
            {
               return false;
            }

        }

        /// <summary>
        /// Method that Puts file on target directory.  If file exists in target directory it will be
        /// deleted prior to the copy
        /// </summary>
        /// <param name="fromfile">path of file to put (includes file name)</param>
        /// <param name="targetfile">path of target file (includes file name)</param>
        /// <returns></returns>
        public static bool PutFile(string fromfiledir, string tofiledir)
        {
            if (System.IO.File.Exists(fromfiledir))
            {
                if (System.IO.File.Exists(tofiledir))
                {
                    System.IO.File.Delete(tofiledir);
                }
                System.IO.File.Copy(fromfiledir, tofiledir);
            }

            // Verify File Copied
            if (System.IO.File.Exists(tofiledir))
            {
                results = true;
            }
            else
            {
                results = false;
            }

            return results;
        }    

        /// <summary>
        /// 
        /// </summary>
        /// <param name="directory"></param>
        /// <param name="contains"></param>
        /// <returns></returns>
        public static List<String> GetFileList(string directory, string contains)
        {           
            List<String> filelist = new List<String>();
           
           string[] list = System.IO.Directory.GetFiles(directory, contains);
           for (int i = 0; i < list.Length; i++)
           {
               filelist.Add(list[i]);
           }
           filelist.Sort();
           return filelist;

        }

        /// <summary>
        /// 
        /// </summary>
        /// <param name="directory"></param>
        /// <param name="contains"></param>
        /// <returns></returns>
        public static string[] GetFileList(string directory)
        {           
            string[] list = System.IO.Directory.GetFiles(directory);
            return list;
        }

        /// <summary>
        /// Method to move all files from one dir to another
        /// </summary>
        /// <param name="fromdir">move from directory</param>
        /// <param name="todir">move to directory</param>
        /// <param name="identifier">Prepended to filename</param>
        /// <returns></returns>
        public static bool MoveAllFiles(string fromdir, string todir, string identifier)
        {           
            string[] list = GetFileList(fromdir);
            string date = DateTime.Now.ToString("yyMMddhhmmss");
            Console.Write(date);
            foreach (string x in list)
            {
                string[] z = x.Split('\\');
                string newfile = todir + "\\" + identifier + date + z[z.Length - 1];
                Console.Write(newfile + Environment.NewLine);

                if (!File.Exists(newfile))
                {
                    System.IO.File.Move(x, newfile);
                }
                else
                {
                  //  newfile = newfile.Substring(0, (newfile.Length -4)) + StringUtil.RandomString(5);
                    System.IO.File.Move(x, newfile);

                }
            }

           // Console.Write(GetFileList(fromdir).Length);

            if (GetFileList(fromdir).Length == 0)
            {
                return true;
            }
            else { return false; }

        }

        /// <summary>
        /// Method to verify if a file exists. 
        /// </summary>
        /// <param name="path"></param>
        /// <returns></returns>
        public static bool FileExists(string path)
        {           
            if (File.Exists(path))
            {
                return true;
            }
            else
            {
                return false; 
            }

        }

        public static DateTime SetFileAge(string path, string fileAge)
        {
            DateTime newDateTime = DateTime.Now - TimeSpan.Parse(fileAge);
            System.IO.File.SetCreationTime(path, newDateTime);
            return newDateTime;
        }


    }
}
