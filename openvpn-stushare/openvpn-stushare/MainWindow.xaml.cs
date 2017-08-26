using Newtonsoft.Json;
using Newtonsoft.Json.Linq;
using System;
using System.Diagnostics;
using System.Net.NetworkInformation;
using System.Resources;
using System.Text;
using System.Threading;
using System.Windows;
using System.Windows.Forms;
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

        private void Show(object sender, EventArgs e)
        {
            this.Visibility = System.Windows.Visibility.Visible;
            this.ShowInTaskbar = true;
            this.Activate();
            Show();
            WindowState = WindowState.Normal;
        }

        private void Hide(object sender, EventArgs e)
        {
            Hide();
            this.ShowInTaskbar = false;
            this.Visibility = System.Windows.Visibility.Hidden;
        }

        private void Close(object sender, EventArgs e)
        {
            notifyIcon.Dispose();
            System.Windows.Application.Current.Shutdown();
        }
        private NotifyIcon notifyIcon;


        private void Window_Loaded(object sender, RoutedEventArgs e1)
        {

            notifyIcon = new NotifyIcon();
            notifyIcon.Text = "校园网分享客户端 " + Functions.version;

            var file = Properties.Resources.ResourceManager.GetObject("vpn");
            notifyIcon.Icon = (System.Drawing.Icon)(file);

            notifyIcon.Visible = true;
            //打开菜单项
            System.Windows.Forms.MenuItem open = new System.Windows.Forms.MenuItem("打开");
            open.Click += new EventHandler(Show);
            //退出菜单项
            System.Windows.Forms.MenuItem exit = new System.Windows.Forms.MenuItem("退出");
            exit.Click += new EventHandler(Close);
            //关联托盘控件
            System.Windows.Forms.MenuItem[] childen = new System.Windows.Forms.MenuItem[] { open, exit };
            notifyIcon.ContextMenu = new System.Windows.Forms.ContextMenu(childen);

            this.notifyIcon.MouseDoubleClick += new System.Windows.Forms.MouseEventHandler((o, e) =>
            {
                if (e.Button == MouseButtons.Left) this.Show(o, e);
            });

            Thread thread = new Thread(new ThreadStart(getServerData));
            thread.Start();
        }
        public void getServerData()
        {

            string retData = Functions.HttpGet(Functions.URL + "/user/api/getserver", "");
            JObject jo = (JObject)JsonConvert.DeserializeObject(retData);
            Dispatcher.BeginInvoke(new Action(delegate
            {
                addMsg("刷新服务器列表...");
                listView.Items.Clear();
                int count = 0;
                foreach (JObject item in jo["rows"])
                {
                    count++;
                    listView.Items.Add(new serverData(item["name"].ToString(), item["ip"].ToString(), item["count"].ToString(), item["config"].ToString()));
                }
                addMsg("刷新完成,共" + count.ToString() + "台");
            }));
        }
        class serverData
        {
            private string mName, mIp, mCount;
            private string mConfig;

            public serverData(string name, string ip, string count, string config)
            {
                mName = name;
                mIp = ip;
                mCount = count;
                mConfig = config;
            }
            public string serverName
            {
                get
                {
                    return mName;
                }
            }
            public string humNumber
            {
                get
                {
                    return mCount;
                }
            }
            public string config
            {
                get
                {
                    return mConfig;
                }
            }
            public string ping
            {
                get
                {
                    Ping pingSender = new Ping();
                    PingOptions options = new PingOptions();
                    options.DontFragment = true;
                    //测试数据
                    string data = "test data abcabc";
                    byte[] buffer = Encoding.ASCII.GetBytes(data);
                    PingReply reply = pingSender.Send(mIp, 100, buffer, options);
                    if (reply.Status == IPStatus.Success)
                    {
                        return reply.RoundtripTime.ToString() + "ms";
                    }
                    else
                    {
                        return "GG";
                    }
                }
            }
        }
        private void Window_Closing(object sender, System.ComponentModel.CancelEventArgs e)
        {
            Hide();
            e.Cancel = true;
        }

        private void Window_StateChanged(object sender, EventArgs e)
        {
            if (WindowState == WindowState.Minimized)
            {
                Hide();
            }
        }
        private void f5_Click(object sender, RoutedEventArgs e)
        {
            Thread thread = new Thread(new ThreadStart(getServerData));
            thread.Start();
        }

        private void test_Click(object sender, RoutedEventArgs e)
        {
            serverData s = (serverData)listView.Items.GetItemAt(0);

        }

        private void listView_MouseDoubleClick(object sender, System.Windows.Input.MouseButtonEventArgs e)
        {
            start_Click(sender, e);
        }
        private string mConfig;
        private void startOpenVpn()
        {
            Functions.WriteFile("tmp.ovpn", mConfig);
            Process process = new Process();
            process.StartInfo.FileName = "bin/openvpn.exe";
            process.StartInfo.Arguments = "--config tmp.ovpn";
            process.StartInfo.UseShellExecute = false;
            process.StartInfo.RedirectStandardOutput = true;
            process.StartInfo.RedirectStandardInput = true;
            process.StartInfo.CreateNoWindow = true;
            process.OutputDataReceived += new DataReceivedEventHandler(process_OutputDataReceived);
            process.Start();
            process.BeginOutputReadLine();
            Thread.Sleep(1000);
            process.StandardInput.WriteLine(Functions.User);
            Thread.Sleep(500);
            process.StandardInput.WriteLine(Functions.Pwd);
            process.WaitForExit();
            process.Close();
            Functions.DeleteFile("tmp.ovpn");
        }

        private void process_OutputDataReceived(object sender, DataReceivedEventArgs e)
        {
            if (e.Data != null)
            {
                try
                {
                    System.Diagnostics.Debug.WriteLine(e.Data.ToString());
                    Action act = () =>
                    {
                        addMsg(e.Data.ToString());
                        if (e.Data.ToString().IndexOf("auth-failure") >= 0)
                        {
                            addMsg("你的账号已经过期或者并未开通,请前往官网开通------>" + Functions.URL + "/user/money/vip");
                        }
                    };
                    msgBox.Dispatcher.Invoke(act);
                }
                catch (Exception)
                {
                    throw;
                }

            }
        }

        private void start_Click(object sender, RoutedEventArgs e)
        {
            serverData s = (serverData)listView.SelectedItem;
            if (s == null)
            {
                System.Windows.Forms.MessageBox.Show("请先选中服务器", "提示");
            }
            else
            {
                mConfig = s.config;
                Thread ovThread = new Thread(startOpenVpn);
                ovThread.Start();
            }
        }

        private void addMsg(string msg)
        {
            msgBox.AppendText(msg + "\n");
            msgBox.ScrollToEnd();
        }
    }
}
