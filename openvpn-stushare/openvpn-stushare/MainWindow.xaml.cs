using System;
using System.Diagnostics;
using System.IO;
using System.IO.Pipes;
using System.Threading;
using System.Windows;

namespace openvpn_stushare
{
    /// <summary>
    /// MainWindow.xaml 的交互逻辑
    /// </summary>
    public partial class MainWindow : Window
    {
        public MainWindow()
        {
            InitializeComponent();
        }

        private void start_Click(object sender, RoutedEventArgs e)
        {
            Thread ovThread = new Thread(startOpenVpn);
            ovThread.Start();
        }

        private void startOpenVpn()
        {
            Process process = new Process();
            process.StartInfo.FileName = "openvpn.exe";
            process.StartInfo.Arguments = "--config user.ovpn";
            process.StartInfo.UseShellExecute = false;
            process.StartInfo.RedirectStandardOutput = true;
            process.StartInfo.RedirectStandardInput = true;
            process.StartInfo.CreateNoWindow = true;
            process.OutputDataReceived += new DataReceivedEventHandler(process_OutputDataReceived);
            process.Start();
            process.BeginOutputReadLine();
            Thread.Sleep(1000);
            process.StandardInput.WriteLine("Farmer");
            Thread.Sleep(500);
            process.StandardInput.WriteLine("23333");
            process.WaitForExit();
            process.Close();
        }

        private void process_OutputDataReceived(object sender, DataReceivedEventArgs e)
        {
            if (e.Data != null)
            {
                try
                {
                    System.Diagnostics.Debug.WriteLine(e.Data.ToString());
                    Action act = () => { textBox.Text += e.Data.ToString()+"\n"; };
                    textBox.Dispatcher.Invoke(act);
                }
                catch (Exception)
                {
                    throw;
                }
                
            }
        }
    }
}
