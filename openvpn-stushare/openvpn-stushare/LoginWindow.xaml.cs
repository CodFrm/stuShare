using Newtonsoft.Json;
using Newtonsoft.Json.Linq;
using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Shapes;

namespace openvpn_stushare
{
    /// <summary>
    /// LoginWindow.xaml 的交互逻辑
    /// </summary>
    public partial class LoginWindow : Window
    {
        public LoginWindow()
        {
            InitializeComponent();
        }

        private void Reg_Click(object sender, RoutedEventArgs e)
        {
            System.Diagnostics.Process.Start("explorer.exe", Functions.URL+"/index/login/register");
        }
        MainWindow mwin = new MainWindow();
        private void Login_Click(object sender, RoutedEventArgs e)
        {
            string retData=Functions.HttpPost(Functions.URL+ "/index/login/login", "user="+Edit_User.Text+"&pwd="+Edit_Pwd.Password);
            JObject jo = (JObject)JsonConvert.DeserializeObject(retData);
            string zone = jo["code"].ToString();
            string zone_en = jo["msg"].ToString();
            MessageBox.Show(zone_en,"提示");
            if (zone == "0")
            {
                Functions.WriteIni("data","user",Edit_User.Text);
                if (savep.IsChecked==true)
                {
                    Functions.WriteIni("check", "savep", "true");
                    Functions.WriteIni("data", "pwd", Edit_Pwd.Password);
                }
                else
                {
                    Functions.WriteIni("check", "savep", "false");
                }
                Functions.User = Edit_User.Text;
                Functions.Pwd = Edit_Pwd.Password;
                Application.Current.MainWindow = mwin;
                mwin.Show();
                this.Close();
            }
            
        }

        private void Window_Loaded(object sender, RoutedEventArgs e)
        {
            Edit_User.Text = Functions.ReadIni("data","user");
            if (Functions.ReadIni("check","savep")=="true")
            {
                Edit_Pwd.Password = Functions.ReadIni("data","pwd");
                savep.IsChecked = true;
            }
        }

        private void Window_Closing(object sender, System.ComponentModel.CancelEventArgs e)
        {
            if (!mwin.IsVisible)
            {
                System.Environment.Exit(0);
            }
        }
    }
}
