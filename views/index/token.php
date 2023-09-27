<?php
/** @var $this yii\web\View */
/** @var $model crud\modules\pay\models\Order */
/** @var $url string */
/** @var $file array */
/** @var $code  */
/** @var $submenu string */
use imessage\assets\VueAsset;
VueAsset::register($this);
?>
    <div class="wrap" style="display: none">

        <h1 class="wp-heading-inline">{{title}}</h1>
        <button class="page-title-action  thickbox" @click="flush">清空</button>
        <button class="page-title-action  thickbox" @click="refresh">重置</button>
        <hr class="wp-header-end">
        <ul class="nav-tab-wrapper wp-clearfix">
            <a v-for="(url,index) in navTab"
               :href="'/wp-admin/admin.php?page=' +url.url"
               :class="(page == url.url)?'nav-tab nav-tab-active':'nav-tab'">{{url.text}}</a>
        </ul>

        <div class="tablenav top" style="margin-bottom: 0px">
            <div class="alignleft actions bulkactions">
                <input type="submit" value="新增" class="button action" @click="add">
                <input type="submit" value="刷新" class="button action" @click="init">
                <input type="submit" value="删除" class="button action" @click="deleteAll">
            </div>
            <div class="tablenav-pages" style="margin-bottom: 0px">
                <span class="displaying-num">{{pageLength}}条记录</span>
                <select  class="tablenav-pages-navspan button" v-model="tableSize" >
                    <option  value="10">10</option>
                    <option  value="15">15</option>
                    <option  value="20">20</option>
                    <option  :value="pageLength">All</option>
                </select>
                <span class="pagination-links">
                <button :class="'tablenav-pages-navspan button ' +((index ==1) ?'disabled':'')" @click="index =1">«</button>
                <button :class="'tablenav-pages-navspan button '+((index >1) ?'':'disabled')" @click="(index <=pageSum && index>1)?(index =index-1):''">‹</button>
                <span class="paging-input">
                    第<label for="current-page-selector" class="screen-reader-text">当前页</label>
                    <input  type="text" name="paged" size="1" v-model="index" class="current-page">
                    <span class="tablenav-paging-text">页,共<span class="total-pages">{{pageSum}}</span>页</span>
                </span>
                <button :class="'next-page button '+((index<pageSum)?'':'disabled')" @click="(index <pageSum)?(index =index+1):''">›</button>
                <button :class="'tablenav-pages-navspan button ' +((index<pageSum)?'':'disabled') " @click="index =pageSum">»</button>
            </span>
            </div>
        </div>

        <table class="wp-list-table widefat fixed striped table-view-list" style="margin-top: 5px">
            <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                    <input id="cb-select-all-1" type="checkbox">
                </td>
                <td  class="manage-column" style="width: 30px"><span>序号</span></td>
                <td  class="manage-column"><span>名称</span></td>
                <td  class="manage-column" style="width: 300px"><span>Token</span></td>

                <td  class="manage-column"><span>请求数</span></td>
                <td  class="manage-column"><span>成功数</span></td>
                <td  class="manage-column"><span>速率</span></td>
                <td  class="manage-column"><span>启/禁用</span></td>
                <td  class="manage-column"><span>通知</span></td>
                <td  class="manage-column"><span>状态</span></td>
                <td  class="manage-column" style="width: 150px"><span>刷新时间</span></td>
            </tr>
            </thead>
            <tbody id="the-list">
            <template v-for="(item,index) in rulesArray ">
                <tr  v-if="index>=startNumber && index<=endNumber">
                    <th scope="row" class="check-column">
                        <label class="screen-reader-text" for="cb-select-1">选择</label>
                        <input id="cb-select-1" type="checkbox" name="post[]" @click="add_key(item.token)">
                        <div class="locked-indicator">
                            <span class="locked-indicator-icon" aria-hidden="true"></span>
                        </div>
                    </th>
                    <td class="manage-column" data-colname="名称">{{index+1}}</td>
                    <td class="manage-column" data-colname="名称">{{item.name}}</td>
                    <td class="manage-column" data-colname="token">{{item.token}}</td>
                    <td class="manage-column" data-colname="请求数">{{item.get_number}}</td>
                    <td class="manage-column" data-colname="成功数">{{item.success_number}}</td>
                    <td class="manage-column" data-colname="速率">{{item.route}}</td>
                    <td class="manage-column" data-colname="启用/禁用">
                        <span :style="'background-color: '+((item.disable ==0)?'#52accc':'#cf4944')+';color: white;padding: 5px;border-radius: 5px'"
                              v-html="(item.disable ==1)?'禁用':'启用'" @click="disableChange(item.token)"></span>
                    </td>
                    <td class="manage-column" data-colname="通知">{{item.message}}</td>
                    <td class="manage-column" data-colname="状态" v-html="html(item.time)"></td>
                    <td class="manage-column" data-colname="刷新时间" v-html='timestampToTime(item.time)'></td>
                </tr>
            </template>

            </tbody>
            <tfoot>
            <tr>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                    <input id="cb-select-all-1" type="checkbox">
                </td>
                <td  class="manage-column" style="width: 30px"><span>序号</span></td>
                <td  class="manage-column"><span>名称</span></td>
                <td  class="manage-column" style="width: 300px"><span>Token</span></td>

                <td  class="manage-column"><span>请求数</span></td>
                <td  class="manage-column"><span>成功数</span></td>
                <td  class="manage-column"><span>速率</span></td>
                <td  class="manage-column"><span>启/禁用</span></td>
                <td  class="manage-column"><span>通知</span></td>
                <td  class="manage-column"><span>状态</span></td>
                <td  class="manage-column" style="width: 150px"><span>刷新时间</span></td>
            </tr>
            </tr>
            </tfoot>
        </table>
    </div>
<?php
$js=<<<JS
new Vue({
    el: '.wrap',
    data(){
        return {
            title:"iMessage",
            rules:{},
            tableSize:10,
            index:1,
            ids:[],
            navTab:{PHP_CODE_submenu},
        }
    },
    watch:{
       index(value){
           console.log([this.pageLength,this.index,this.startNumber,this.endNumber])
       } 
    },
    created() {
        // 备份初始数据
        document.querySelector('.wrap').style.display = 'block';
    },
    computed:{
        params(){
            let params ={};
            const urlParams = new URLSearchParams(window.location.search);
            for (const [key, value] of urlParams) {
              params[key] = value;
            }
            return params;
        },
        page(){
            return this.params.page||"";
        },
        pageLength(){
            return Object.keys(JSON.parse( JSON.stringify(this.rules))).length;
        },
        pageSum(){
            return Math.ceil(this.pageLength / this.tableSize)
        },
        startNumber(){
            return (this.index -1)*this.tableSize;
        },
        endNumber(){
            return (this.index)*this.tableSize -1;
        },
        rulesArray(){
           let arr =[];
           for (let x in this.rules){
                let tmp = (this.rules)[x];
                arr.push({
                    'token':x || '',
                    ...tmp,
                    })
            }
           arr.sort((a, b) => b.time - a.time);
           return arr; 
        },
    },
    methods:{
        flush(){
            if(confirm('确定要清空Token吗？')){
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'GET',
                    data: {
                      action: 'index/token',
                      type:"flush",
                    },
                    dataType: 'json',
                    success: (res) => {
                         this.rules={}
                      alert(res.message)
                    },
                    error: (res) => {
                      console.log(res);
                    }
                })
            }
        },
        refresh(){
            jQuery.ajax({
                url: ajaxurl,
                type: 'GET',
                data: {
                  action: 'index/token',
                  type:"Refresh",
                },
                dataType: 'json',
                success: (res) => {
                     this.rules=res.data
                  alert(res.message)
                },
                error: (res) => {
                  console.log(res);
                }
            })
        },
        init(){
            jQuery.ajax({
                url: ajaxurl,
                type: 'GET',
                data: {
                  action: 'index/token',
                  type:"get",
                },
                dataType: 'json',
                success: (res) => {
                  
                   if(res.code ==1){
                       // console.log(res.data)
                       this.rules=res.data
                   }
                   console.log()
                },
                error: (res) => {
                  console.log(res);
                }
            })
        },
        is_show(itemIndex){
            let is_a =((itemIndex>=this.startNumber)&&(itemIndex<=this.endNumber)&&(itemIndex<=this.pageSum-1));
            console.log(is_a)
            return is_a
           
        },
        add(){
            var name = window.prompt("请填写token名称","defaultText"); 
            if(name.length<=0){
                alert('名称不能为空')
            }
            var route = window.prompt("请填写请求限制规则\\n    例如:\"70/5\" 5分钟内最多请求70次\\n    空:表示没有限制","");
             jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                  action: 'index/token',
                  type:"create",
                  name:name,
                  route:route,
                },
                dataType: 'json',
                success: (res) => {
                    console.log(res.data)
                   if(res.code ==1){
                       this.rules=res.data
                   }
                },
                error: (res) => {
                }
            })
        },
        deleteAll(){
            console.log(this.ids.length)
          if(this.ids.length >0){
              jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                  action: 'index/token',
                  type:"delete",
                 ids:this.ids,
                },
                dataType: 'json',
                success: (res) => {
                    console.log(res.data)
                   if(res.code ==1){
                       location.reload()
                       // this.rules=res.data
                   }
                },
                error: (res) => {
                }
            })
          }else {
              alert('请选择记录')
          } 
        },
        add_key(key){
            this.ids.push(key) 
            this.ids =[...new Set(this.ids)]  
            console.log(this.ids)
        },
        timestampToTime(timestamp) {
        
            var date = new Date(timestamp * 1000); // 时间戳为10位需*1000，13位的话不需要
            let Y = date.getFullYear() + "-";
            let M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
            let D = (date.getDate() < 10 ? '0' + date.getDate() : date.getDate()) + " ";
            let H = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ":";
            let MIN = (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + ":";
            let S = (date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds());
            return Y + M + D + H + MIN + S;
        },
        disableChange(key){
            let disable = (this.rules)[key]['disable'] || 0;
             jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                  action: 'index/token',
                  type:"disableChange",
                  token:key,
                },
                dataType: 'json',
                success: (res) => {
                   if(res.code ==1){
                       (this.rules)[key]['disable'] =!disable
                   }
                },
                error: (res) => {
                }
            })
        },
        html(time){
            var currentTimestamp = Math.floor(Date.now() / 1000);
            if(Math.floor((currentTimestamp - time) / 60) >5){
                return "<span style='background-color: rgb(207, 73, 68); color: white; padding: 5px; border-radius: 5px;'>离线</span>"
            }
            return '<span style="background-color: rgb(82, 172, 204); color: white; padding: 5px; border-radius: 5px;">在线</span>'
        }
    },
    mounted(){
        this.init();
        setInterval(this.init,5000)
    }
});
JS;
$this->registerJs(php_code_to_js_code(get_defined_vars()));