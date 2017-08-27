using Newtonsoft.Json;
using Newtonsoft.Json.Linq;
using System.Windows;
using System.Web;
namespace openvpn_stushare
{
    /// <summary>
    /// FeedBackWindow.xaml 的交互逻辑
    /// </summary>
    public partial class FeedBackWindow : Window
    {
        public FeedBackWindow()
        {
            InitializeComponent();
        }

        private void post_Click(object sender, RoutedEventArgs e)
        {
            string retData = Functions.HttpPost(Functions.URL+"/user/api/feedback","type=1&call="+ HttpUtility.UrlEncode(call.Text)+"&msg=" + HttpUtility.UrlEncode(msg.Text));
            JObject jo = (JObject)JsonConvert.DeserializeObject(retData);
            string zone = jo["code"].ToString();
            string zone_en = jo["msg"].ToString();
            MessageBox.Show(zone_en, "提示");
            if (zone == "0")
            {
                Close();
            }
        }
    }
}
