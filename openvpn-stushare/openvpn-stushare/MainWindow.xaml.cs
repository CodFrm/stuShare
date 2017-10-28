using Microsoft.Win32;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;
using System;
using System.Diagnostics;
using System.IO;
using System.Net;
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
            try
            {
                openVpn.Kill();
                openVpn.Close();
            }
            catch (Exception)
            {

            }
            System.Environment.Exit(0);
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

            try
            {
                string retData = Functions.HttpGet(Functions.URL + "/user/api/getserver", "");
                JObject jo = (JObject)JsonConvert.DeserializeObject(retData);
                Action act = () =>
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
                };
                Dispatcher.Invoke(act);
            }
            catch (Exception e)
            {
                addMsg(e.Message);
            }

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
                    PingReply reply = pingSender.Send(mIp, 1000, buffer, options);
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

        private void listView_MouseDoubleClick(object sender, System.Windows.Input.MouseButtonEventArgs e)
        {
            start_Click(sender, e);
        }
        private string mConfig;
        private bool mIsRun = false;
        private Process openVpn;
        private bool mIsSuccess = false;
        private void startOpenVpn()
        {
            if (mIsRun)
            {
                if (mIsSuccess)
                {
                    addMsg("连接成功了");
                }
                else
                {
                    addMsg("正在呢");
                }
                return;
            }
            mIsRun = true;
            mIsSuccess = false;
            try
            {
                string tmpStr = Functions.ReadIni("openvpn", "model") ;
                int model = int.Parse(tmpStr==""?"0":tmpStr);
                Functions.KillProcess("openvpn");
                setState("vpn_wait");
                string strAppPath = System.Windows.Forms.Application.StartupPath;
                if (model == 0)
                {
                    Functions.WriteFile(strAppPath + "\\pass.txt", Functions.User + "\r\n" + Functions.Pwd + "\r\n");
                    mConfig = mConfig.Replace("auth-user-pass", "auth-user-pass pass.txt");
                }
                Functions.WriteFile(strAppPath + "\\tmp.ovpn", mConfig);

                openVpn = new Process();
                openVpn.StartInfo.FileName = getOvpnPath();
                openVpn.StartInfo.Arguments = "--config \"" + strAppPath + "\\tmp.ovpn\"";
                openVpn.StartInfo.UseShellExecute = false;
                openVpn.StartInfo.RedirectStandardOutput = true;
                openVpn.StartInfo.RedirectStandardInput = true;
                openVpn.StartInfo.CreateNoWindow = true;
                openVpn.OutputDataReceived += new DataReceivedEventHandler(process_OutputDataReceived);
                openVpn.Exited += OpenVpn_Exited;
                openVpn.Start();
                openVpn.BeginOutputReadLine();
                if (model == 1)
                {
                    Thread.Sleep(500);
                    openVpn.StandardInput.WriteLine(Functions.User);
                    Thread.Sleep(500);
                    openVpn.StandardInput.WriteLine(Functions.Pwd);
                }
                else if (model == 0)
                {
                    Thread.Sleep(1000);
                    Functions.DeleteFile(strAppPath + "\\pass.txt");
                }
                Functions.DeleteFile(strAppPath + "\\tmp.ovpn");
                int timer = 0;
                while (mIsRun)
                {
                    timer += 1;
                    Thread.Sleep(1000);
                    if (timer >= 15)
                    {
                        if (!mIsSuccess)
                        {
                            addMsg("连接超时了,换个服务器吧,这台可能GG了");
                            openVpn.Close();
                        }
                        else
                        {
                            openVpn.WaitForExit();
                            openVpn.Kill();
                            openVpn.Close();
                        }
                        break;
                    }
                }

            }
            catch (Exception e)
            {
                if (e.Message.IndexOf("找不到指定的文件") >= 0)
                {
                    addMsg("未找到文件 尝试安装openvpn");
                    installOpenvpn();
                }
                else
                {
                    addMsg(e.Message);
                }
            }
            mIsRun = false;
            setState("vpn");
        }

        private void setState(string str)
        {
            var file = Properties.Resources.ResourceManager.GetObject(str);
            notifyIcon.Icon = (System.Drawing.Icon)(file);
        }

        private void OpenVpn_Exited(object sender, EventArgs e)
        {
            mIsRun = false;
            setState("vpn");
        }

        private void installOpenvpn()
        {
            addMsg("正准备安装openvpn...");
            addMsg("正在下载openvpn安装程序...");
            // 设置参数
            HttpWebRequest request = WebRequest.Create(Functions.URL + "/static/openvpn.exe") as HttpWebRequest;
            //发送请求并获取相应回应数据
            HttpWebResponse response = request.GetResponse() as HttpWebResponse;
            //直到request.GetResponse()程序才开始向目标网页发送Post请求
            Stream responseStream = response.GetResponseStream();
            //创建本地文件写入流
            Stream stream = new FileStream("install.exe", FileMode.Create);
            byte[] bArr = new byte[1024];
            int length = int.Parse(response.GetResponseHeader("Content-Length"));
            int downloadLength = 0;
            int downloadSpeed = 0;
            int size = responseStream.Read(bArr, 0, (int)bArr.Length);
            while (size > 0)
            {
                downloadLength += size;
                if (((float)downloadLength / (float)length) * 40 > downloadSpeed)
                {
                    addMsg("=", false);
                    downloadSpeed++;
                }
                stream.Write(bArr, 0, size);
                size = responseStream.Read(bArr, 0, (int)bArr.Length);
            }
            stream.Close();
            responseStream.Close();
            addMsg("\n下载完成,准备进行安装...");
            Functions.RunExe("install.exe", "/S /D=openvpn");
            addMsg("安装完成,请重新尝试运行");
            Functions.DeleteFile("install.exe");
        }

        private string getOvpnPath()
        {
            try
            {
                RegistryKey localMachine = RegistryKey.OpenBaseKey(RegistryHive.LocalMachine, RegistryView.Registry64);
                RegistryKey Keys = localMachine.OpenSubKey("SOFTWARE\\OpenVPN");
                string path = "";
                path = Keys.GetValue("exe_path", "").ToString();
                addMsg(path);
                return path;
            }
            catch (Exception)
            {
                return "openvpn.exe";
            }

        }
        private void process_OutputDataReceived(object sender, DataReceivedEventArgs e)
        {
            if (e.Data != null)
            {
                try
                {

                    addMsg(e.Data.ToString());
                    if (e.Data.ToString().IndexOf("auth-failure") >= 0)
                    {
                        addMsg("你的账号已经过期或者并未开通,请前往官网开通------>" + Functions.URL + "/user/money/vip");
                        mIsRun = false;
                        setState("vpn");
                    }
                    else if (e.Data.ToString().IndexOf("Exiting due to fatal error") >= 0)
                    {
                        addMsg("软件错误,请反馈错误日志");
                        mIsRun = false;
                        setState("vpn");
                    }
                    else if (e.Data.ToString().IndexOf("succeeded") >= 0)
                    {
                        if (!mIsSuccess)
                        {
                            addMsg("连接成功了");
                            setState("vpn_success");
                        }
                        mIsSuccess = true;
                    }
                    else if (e.Data.ToString().IndexOf("WARNING: cannot stat file 'pass.txt':") >= 0)
                    {
                        Functions.WriteIni("openvpn", "model", "1");
                        openVpn.Close();
                        mIsRun = false;
                        addMsg("启动失败,更换启动模式,请再重新点击连接");
                    }
                    else if (e.Data.ToString().IndexOf("Failed retrieving username or password") >= 0)
                    {
                        Functions.WriteIni("openvpn", "model", "0");
                        openVpn.Close();
                        mIsRun = false;
                        addMsg("启动失败,更换启动模式,请再重新点击连接");
                    }

                }
                catch (Exception exc)
                {
                    addMsg(exc.Message);
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

        private void addMsg(string msg, bool line = true)
        {
            Action act = () =>
            {
                msgBox.AppendText(msg + (line ? "\n" : ""));
                msgBox.ScrollToEnd();
            };
            msgBox.Dispatcher.Invoke(act);
        }

        private void stop_Click(object sender, RoutedEventArgs e)
        {
            try
            {
                openVpn.Kill();
                openVpn.Close();
            }
            catch (Exception)
            {

            }
            addMsg("已停止");
            mIsRun = false;
        }

        private void feedback_Click(object sender, RoutedEventArgs e)
        {
            FeedBackWindow feed = new FeedBackWindow();

            feed.ShowDialog();
        }
    }
}
