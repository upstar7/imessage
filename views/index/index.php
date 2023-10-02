<?php
/** @var $this  */
/** @var $x string */
/** @var $y string */
/** @var $pie string */
/** @var $total int */
/** @var $success int */
/** @var $sum  int */
/** @var $n int */
/** @var $baifenbi string */
/** @var $submenu string */
use imessage\assets\ChartAsset;
use imessage\assets\VueAsset;
use imessage\assets\JvectormapAsset;

VueAsset::register($this);
ChartAsset::register($this);
JvectormapAsset::register($this);
JvectormapAsset::addJsFile($this,
    "/maps/region/cn/cn_merc.js",
    ["depends"=>"imessage\assets\JvectormapAsset"]
);
JvectormapAsset::addJsFile($this,
    "/maps/city/world/world-merc.js",
    ["depends"=>"imessage\assets\JvectormapAsset"]
);
$token = get_option('imessage_group_ipinfo_token');
?>

<div class="wrap">

    <h1 class="wp-heading-inline">iMessage</h1>

    <hr class="wp-header-end">
    <ul class="nav-tab-wrapper wp-clearfix" id="app">
        <a v-for="(url,index) in navTab"
           :href="'/wp-admin/admin.php?page=' +url.url"
           :class="(page == url.url)?'nav-tab nav-tab-active':'nav-tab'">{{url.text}}</a>
    </ul>
    <div style="margin-top: 20px">
        <div class="the-list" >
            <div class="plugin-card plugin-card-classic-editor">
                <div class="plugin-card-top" id="pie"  style="width: auto;height: 300px">
                </div>
                <div class="plugin-card-bottom">
                    <div class="vers column-rating">
                        <div class="star-rating">
                            <span class="screen-reader-text">5.0星（基于1,119个评级）</span>
                            <?php

                                for ($c =1;$c<=5;$c++){
                                    if($c<=$n){
                            ?>
                            <div class="star star-full" aria-hidden="true"></div>
                                        <?php }else{?>
                            <div class="star star-empty" aria-hidden="true"></div>
                            <?php }} ?>
                        </div>
                        <span class="num-ratings" aria-hidden="true">(<?= $success?>)</span>
                    </div>
                    <div class="column-updated">
                        完成比例:<strong><?=$baifenbi ?></strong>
                    </div>
                    <div class="column-downloaded">今天iMessage完成情况</div>
                    <div class="column-compatibility">
                        <span class="compatibility-compatible">
                            共<strong><?= $total ?></strong>条,已发送
                            <strong><?= $success?></strong>条,待发送<strong><?= ($total- $success)?>条</strong><br>
                            <?= date('Y-m-d') ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="plugin-card plugin-card-classic-editor">
                <div class="plugin-card-top" id="bar"  style="width: auto;height: 300px">
                </div>
                <div class="plugin-card-bottom">
                    <div class="vers column-rating">
                        <div class="star-rating">
                            <span class="screen-reader-text"></span>
                            <div class="star star-full" aria-hidden="true"></div>
                            <div class="star star-full" aria-hidden="true"></div>
                            <div class="star star-full" aria-hidden="true"></div>
                            <div class="star star-full" aria-hidden="true"></div>
                            <div class="star star-full" aria-hidden="true"></div>
                        </div>
                        <span class="num-ratings" aria-hidden="true">(<?=$sum ?>)</span>
                    </div>
                    <div class="column-updated">
                        共成功发送:<strong><?=$sum?></strong>条
                    </div>
                    <div class="column-downloaded">近7天iMessage完成情况</div>
                    <div class="column-compatibility">
                        <span class="compatibility-compatible">该插件<strong>可同时控制多台手机.全程自动化完成</strong><br>
                            <a >联系我<code>@phpsms123</code></a></span>
                    </div>
                </div>
            </div>

            <div class="plugin-card plugin-card-classic-editor">
                <div class="plugin-card-top" id="markers"  style="width: auto;height: 300px" v-pre>
                </div>
                <div class="plugin-card-bottom">
                    <div class="vers column-rating">
                        <div class="star-rating">
                            <span class="screen-reader-text"></span>
                            <div class="star star-full" aria-hidden="true"></div>
                            <div class="star star-full" aria-hidden="true"></div>
                            <div class="star star-full" aria-hidden="true"></div>
                            <div class="star star-full" aria-hidden="true"></div>
                            <div class="star star-full" aria-hidden="true"></div>
                        </div>
                        <span class="num-ratings" aria-hidden="true"></span>
                    </div>
                    <div class="column-updated">
                        Ip: <strong id="my_ip"></strong>
                    </div>
                    <div class="column-downloaded" id="my_loc"></div>
                    <div class="column-compatibility">
                        <span class="compatibility-compatible">City: <strong id="my_city"></strong><br>
                            Org: <strong id="my_org"></strong>
                        </span>
                    </div>
                </div>
            </div>

            <div class="plugin-card plugin-card-classic-editor">
                <div class="plugin-card-top" id="cn"  style="width: auto;height: 300px" v-pre>
                </div>
                <div class="plugin-card-bottom">
                    <div class="vers column-rating">
                        <div class="star-rating">
                            <span class="screen-reader-text"></span>
                            <div class="star star-full" aria-hidden="true"></div>
                            <div class="star star-full" aria-hidden="true"></div>
                            <div class="star star-full" aria-hidden="true"></div>
                            <div class="star star-full" aria-hidden="true"></div>
                            <div class="star star-full" aria-hidden="true"></div>
                        </div>
                        <span class="num-ratings" aria-hidden="true"></span>
                    </div>
                    <div class="column-updated">

                    </div>
                    <div class="column-downloaded" id="my_loc"></div>
                    <div class="column-compatibility">

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?php
$riqi = date('Y-m-d');
$id = Yii::$app->id;
$js=<<<JS
new Vue({
  el: '#app',
  data() {
    return {
      title: "iMessage",
      activeUrl: 'imessage',
      navTab: {PHP_CODE_submenu},
      echarts: {},
      pie: {
        title: {
          text: '今天iMessage发送情况',
          subtext: '{PHP_CODE_riqi}',
          left: 'left'
        },
        tooltip: {
          trigger: 'item'
        },
        series: [
          {
            name: '今天发送情况',
            type: 'pie',
            radius: '80%',
            data: {PHP_CODE_pie},
            emphasis: {
              itemStyle: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: 'rgba(0, 0, 0, 0.5)'
              }
            }
          }
        ]
      },
      bar: {
        title: { text: 'iMessage完成情况', subtext: '近7天', left: 'left' },
        xAxis: { type: 'category', data: {PHP_CODE_x} },
        yAxis: { type: 'value' },
        series: [
          {
            data: {PHP_CODE_y},
            type: 'bar',
            showBackground: true,
            backgroundStyle: { color: 'rgba(180, 180, 180, 0.2)' }
          }
        ]
      },
      markers:[],
      token:'{PHP_CODE_token}',//'7265d1b29d49c2',
      map:{},
      result:[],
      ips:{PHP_CODE_ips}
    }
  },
  created() {
    // 备份初始数据
    document.querySelector('.wrap').style.display = 'block';
    this.markersInit()
  },
  watch: {
    markers:{
      handler: 'renderMap', 
      deep: true
    }
  },
  computed: {
    params() {
      let params = {};
      const urlParams = new URLSearchParams(window.location.search);
      for (const [key, value] of urlParams) {
        params[key] = value;
      }
      return params;
    },
    page() {
      return this.params.page || "";
    },
    token() {
      if (this.tokens.length > 0) {
        return (this.tokens)[0]
      }
    }
  },
  methods: {
    markersInit(){
      //const self = this;
      $('#markers').vectorMap({
        map: 'world-merc',
        scaleColors: ['#C8EEFF', '#0071A4'],
        normalizeFunction: 'polynomial',
        hoverOpacity: 0.7,
        hoverColor: false,
        backgroundColor: 'transparent',
        regionStyle: {
          initial: {
            fill: 'rgba(210, 214, 222, 1)',
            'fill-opacity': 1,
            stroke: 'none',
            'stroke-width': 0,
            'stroke-opacity': 1
          },
          hover: {
            'fill-opacity': 0.7,
            cursor: 'pointer'
          },
          selected: {
            fill: 'yellow'
          },
          selectedHover: {}
        },
        markerStyle: {
          initial: {
            fill: '#00a65a',
            stroke: '#111'
          }
        },
        markers: this.markers,
        onMarkerLabelShow:(event, label, index)=> {
            let str ='';
            let obj = (this.result)[index]
            console.log(obj)
            for (const key in obj) {
                if (obj.hasOwnProperty(key)) {
                    str += (key.charAt(0).toUpperCase() + key.slice(1)) + ': ' + obj[key] + '<br />';
                }
            }
            console.log(label.html(str))
        },
        onRegionLabelShow: function (e, el, code) {
           // console.log(e, el, code) 
        },
      });
      $('#cn').vectorMap({
        map: 'cn_merc',
        scaleColors: ['#C8EEFF', '#0071A4'],
        normalizeFunction: 'polynomial',
        hoverOpacity: 0.7,
        hoverColor: false,
        backgroundColor: 'transparent',
        regionStyle: {
          initial: {
            fill: 'rgba(210, 214, 222, 1)',
            'fill-opacity': 1,
            stroke: 'none',
            'stroke-width': 0,
            'stroke-opacity': 1
          },
          hover: {
            'fill-opacity': 0.7,
            cursor: 'pointer'
          },
          selected: {
            fill: 'yellow'
          },
          selectedHover: {}
        },
        markerStyle: {
          initial: {
            fill: '#00a65a',
            stroke: '#111'
          }
        },
        markers: this.markers,
        onMarkerLabelShow:(event, label, index)=> {
            let str ='';
            let obj = (this.result)[index]
            console.log(obj)
            for (const key in obj) {
                if (obj.hasOwnProperty(key)) {
                    str += (key.charAt(0).toUpperCase() + key.slice(1)) + ': ' + obj[key] + '<br />';
                }
            }
            console.log(label.html(str))
        },
        onRegionLabelShow: function (e, el, code) {
           // console.log(e, el, code) 
        },
      }); 
      this.map = $('#markers').vectorMap('get', 'mapObject');
    }, 
    MarkerTipShow(event, label, index){
         console.log(event, label, index)
    },
    RegionTipShow(event, label, index){
         console.log(event, label, index)
    },
    copy(data) {
      let n = Math.floor(Math.random() * (data.length + 1));
      const dataToCopy = data[n];
      const tempInput = document.createElement('input');
      tempInput.setAttribute('value', dataToCopy);
      document.body.appendChild(tempInput);
      tempInput.select();
      document.execCommand('copy');
      document.body.removeChild(tempInput);
      alert('复制成功')
    },
    init() {
      console.log('tuytuytuytuytuytuytu');
      jQuery.ajax({
        url: ajaxurl + "?action=index",
        type: 'POST',
        data: {
          type: 'token',
        },
        dataType: 'json',
        success: (res) => {
          if (res.code == 1) {
            this.copy(res.data);
          } else {
            alert(res.message);
          }
        },
        error: (res) => {
          console.log(res);
        }
      })
    },
    renderMap() {
        if (this.map) {
            // $('#markers').empty();
            //this.markersInit();
            //this.map.removeAllMarkers(); // 先删除所有标记
            this.map.addMarkers(this.markers); // 添加新的标记数据
            //this.map.updateSize(); // 更新地图尺寸
            $('#cn').vectorMap('get', 'mapObject').addMarkers(this.markers);
        }

    },
    ip(ip){
        jQuery.ajax({
        url: 'https://ipinfo.io/'+ip,
        type: 'get',
        data: {
          token: this.token,
        },
        xhrFields: {
            withCredentials: false // 禁止携带cookie和session数据
        },
        crossDomain: true, // 允许跨域请求
        dataType: 'json',
        success: (res) => {
            console.log(res)
            if(res.loc !=undefined && res.city != undefined){
                let arr = res.loc.split(',');
                let lat =[parseFloat(arr[0]),parseFloat(arr[1])];
                this.result.push(res)
                this.markers.push({ latLng: lat, name: res.city ,})  
            }
        },
        error: (res) => {
          console.log(res);
        }
      })
    },
    delayedExecution(arr, index) {
      if (index < arr.length) {
        setTimeout(() => {
          this.ip(arr[index]);
          this.delayedExecution(arr, index + 1);
        }, 200); // 1000毫秒（1秒）延迟
      }
    }
  },
  mounted() {
    this.echarts = window.echarts;
    this.echarts.init(document.getElementById('bar')).setOption(this.bar);
    this.echarts.init(document.getElementById('pie')).setOption(this.pie);
     $.get( 'https://ipinfo.io/json',
         (data)=> {
            // 成功获取JSON数据后执行的回调函数
            console.log(data)
            $("#my_city").html(data.city)
            $("#my_ip").html(data.ip)
            $("#my_org").html(data.org)
            $("#my_loc").html(data.loc)
        }
     )
    // setTimeout(() => {
    //   jQuery.get('/wp-json/imessage/api')
    //     .fail((xhr) => {
    //       console.log(xhr)
    //       if (xhr.status !== 200) {
    //         alert("你的插件为通过授权部分功能无法使用" + "\\n" + "请联系@phpsms123");
    //       }
    //     })
    // }, 3000);
    
    let arr = [...new Set(this.ips)]
    this.delayedExecution(arr, 0);
    
  }
})
JS;
$this->registerJs(php_code_to_js_code(get_defined_vars()));
