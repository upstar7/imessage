<?php
/** @var $this imessage\components\View */
use imessage\assets\VueAsset;
VueAsset::register($this);

$n =count(Yii::$app->cache->get('iMessage_data')?:[]);
?>

    <div class="wrap"style="display: none">
        <h1 class="wp-heading-inline">{{title}} <span v-if="settingsFrom.pattern=='high'">剩余数量:<?= $n ?> {{modStr}}</span></h1>
        <hr class="wp-header-end">
        <ul class="nav-tab-wrapper wp-clearfix">
            <a v-for="(url,index) in navTab"
               :href="'/wp-admin/admin.php?page=' +url.url"
               :class="(activeUrl == url.url)?'nav-tab nav-tab-active':'nav-tab'">{{url.text}}</a>
        </ul>
        <div class="tablenav top" style="margin-bottom: 0px">
            <div class="alignleft actions bulkactions">
                <input type="submit" value="新增" class="button action" @click="openAdd" >
            </div>

            <div class="alignleft actions bulkactions">
                <input type="submit" value="批量删除" class="button action" @click="deleteAll">
            </div>

            <div class="alignleft actions bulkactions">
                <input type="submit" value="批量新增" class="button action" @click="openAdds">
            </div>
            <div class="alignleft actions bulkactions"  v-if="settings.pattern=='high'">
                <input type="submit" value="文案" class="button action" @click="setMessage">
            </div>
            <div class="alignleft actions bulkactions" >
                <input type="submit" value="删除已发送" style="color: red"  class="button action" @click="deleteAllYiFaSong">
            </div>
            <div class="alignleft actions bulkactions">
                <input type="submit" value="清空" class="button action" @click="deleteAllDb">
            </div>
            <div class="alignleft actions bulkactions">
                <input type="submit" value="设置" class="button action" @click="openSettings">
            </div>
            <div class="alignleft actions bulkactions">
                <input type="submit" value="测试" class="button action" @click="test">
            </div>
            <div class="alignleft actions bulkactions" v-if="!(settings.pattern=='high')">
                <div class="button last-child"
                     style="background-color: #cf4944;color: white;border-top-right-radius: 0px; border-bottom-right-radius: 0px;"
                     @click="reset(0)" >
                    重置待发送
                </div>
                <div class="button first-child"
                     style="background-color: #52accc;color: white;margin-left: -4px; border-top-left-radius: 0px; border-bottom-left-radius: 0px;"
                     @click="reset(1)">
                    重置已发送
                </div>
            </div>

            <div class="tablenav-pages" style="margin-bottom: 0px">
                <span class="displaying-num">{{tableTotal}}条记录</span>
                <select  class="tablenav-pages-navspan button" v-model="n" >
                    <option  value="10">10</option>
                    <option  value="15">15</option>
                    <option  value="20">20</option>
                    <option  value="50">50</option>
                    <option  value="100">100</option>
                    <option  value="500">500</option>
                    <option  value="all">All</option>
                </select>
                <span class="pagination-links">
                <button :class="'tablenav-pages-navspan button ' +((page ==1) ?'disabled':'')" @click="page =1">«</button>
                <button :class="'tablenav-pages-navspan button '+((page >1) ?'':'disabled')" @click="(page <=pageSum && page>1)?(page =page-1):''">‹</button>
                <span class="paging-input">
                    第<label for="current-page-selector" class="screen-reader-text">当前页</label>
                    <input  type="text" name="paged" size="1" v-model="page" class="current-page">
                    <span class="tablenav-paging-text">页,共<span class="total-pages">{{pageSum}}</span>页</span>
                </span>
                <button :class="'next-page button '+((page<pageSum)?'':'disabled')" @click="(page <pageSum)?(page =page+1):''">›</button>
                <button :class="'tablenav-pages-navspan button ' +((page<pageSum)?'':'disabled') " @click="page =pageSum">»</button>
            </span>
            </div>
        </div>

        <table class="wp-list-table widefat fixed striped table-view-list" style="margin-top: 5px">
            <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1" >全选</label>
                    <input id="cb-select-all-1" type="checkbox"  @change="selectAll">
                </td>
                <td class="manage-column" style="width: 50px"><span>ID</span></td>
                <td class="manage-column" style="width: 150px"><span>手机号</span></td>
                <td class="manage-column" style=""><span>消息</span></td>
                <td class="manage-column" style="width: 100px"><span>客户端</span></td>
                <td class="manage-column" style="width: 80px"><span>获取次数</span></td>
                <td class="manage-column" style="width: 100px"><span>状态</span></td>
                <td class="manage-column" style="width: 150px"><span>创建时间</span></td>
                <td class="manage-column" style="width: 150px"><span>更新时间</span></td>
            </tr>
            </thead>
            <tbody id="the-list">
            <tr  v-for="(value,itemIndex) in tableData">
                <th scope="row" class="check-column">
                    <label class="screen-reader-text" for="cb-select-1">选择</label>
                    <input type="checkbox" name="post[]"  @change="select(value.id)">
                    <div class="locked-indicator">
                        <span class="locked-indicator-icon" aria-hidden="true"></span>
                    </div>
                </th>
                <td class="manage-column"><span>{{value.id}}</span></td>
                <td class="manage-column"><span>{{value.phone}}</span></td>
                <td class="manage-column"><span>{{value.message}}</span></td>
                <td class="manage-column"><span>{{getName(value.token)}}</span></td>
                <td class="manage-column" ><span>{{value.get_number}}</span></td>
                <td class="manage-column">
                        <span :style="'background-color: '+((value.status ==1)?'#52accc':'#cf4944')+';color: white;padding: 5px;border-radius: 5px'"
                              v-html="((value.status ==1)?'已发送':'待发送')" @click="statusChange(value.id)"></span>
                    <span v-if="value.status >1" style="background-color:lavender;padding: 5px;border-radius: 5px" v-html="((value.status ==1)?'':value.status-1)"></span>
                </td>
                <td class="manage-column"><span>{{value.createdTime}}</span></td>
                <td class="manage-column"><span>{{value.updatedTime}}</span></td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                    <input id="cb-select-all-1" type="checkbox"   @change="selectAll">
                </td>
                <td class="manage-column" style="width: 150px"><span>ID</span></td>
                <td class="manage-column"><span>手机号</span></td>
                <td class="manage-column"><span>消息</span></td>
                <td class="manage-column" ><span>客户端</span></td>
                <td class="manage-column" ><span>获取次数</span></td>
                <td class="manage-column"><span>状态</span></td>
                <td class="manage-column"><span>创建时间</span></td>
                <td class="manage-column"><span>更新时间</span></td>
            </tr>
            </tfoot>
        </table>
        <crud-modal :title="modalTitle" :active="active" @submit="submit" @cancel="()=>{active=!active}">
            <form v-if="action=='add'">
                <div style="margin-bottom: 10px">
                    <label>手机号</label>
                    <input type="text" class="regular-text code" placeholder="请填写手机号" v-model="tableFrom.phone">
                </div>
                <div style="margin-bottom: 10px">
                    <label>消息</label>
                    <textarea v-model="tableFrom.message" rows="5" class="large-text code" ></textarea>
                </div>
                <div style="margin-bottom: 10px" v-if="settingsFrom.pattern =='strict'" style="width: 100%">
                    <label>指定客户端</label>
                    <select  class="regular-text code" v-model="tableFrom.token" >
                        <option v-for="(item,key) in token"   :value="key">{{item.name}}</option>
                    </select>
                </div>
                <div style="margin-bottom: 10px">
                    <label>状态</label>
                    <select v-model="tableFrom.status"  class="regular-text code" style="width: 100%">
                        <option value="0">待发送</option>
                        <option value="1">已发送</option>
                    </select>
                </div>
            </form>
            <form v-if="action=='adds'">
                <div style="margin-bottom: 10px">
                    <label>手机号</label>
                    <textarea v-model="addsFrom.phone" rows="10"  placeholder="请用',' ';' '换行'分割" class="large-text code" ></textarea>
                </div>
                <div style="margin-bottom: 10px">
                    <label>消息</label>
                    <textarea v-model="addsFrom.message" rows="5" class="large-text code" ></textarea>
                    <p v-if="settingsFrom.random" style="color: red">开启后在消息文字检'{emoji}'则会插入到指定位置,否则随机插入</p>
                </div>
                <div style="margin-bottom: 10px" v-if="settingsFrom.pattern =='strict'">
                    <label>指定客户端</label>
                    <select  class="regular-text code" v-model="addsFrom.token" style="width: 100%">
                        <option v-for="(item,key) in token"   :value="key">{{item.name}}</option>
                    </select>
                </div>
                <div style="margin-bottom: 10px">
                    <label>状态</label>
                    <select v-model="addsFrom.status" class="regular-text code"  style="width: 100%">
                        <option value="0">待发送</option>
                        <option value="1">已发送</option>
                    </select>
                </div>
            </form>
            <form v-if="action=='settings'">
                <div style="margin-bottom: 10px">
                    <label>正则表达式</label>
                    <input type="text" class="regular-text code" v-model="settingsFrom.preg"   placeholder="\b\d{11}\b" class="large-text code" >
                    <p >批量上传时,识别手机号的正则表达式,如果你不知道请误修改<br><code style="color: red">例如:"\b\d{11}\b" => 12345678901<br> 例如:"\+\d{11}\b" => +12345678901<br>例如:"[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" => qweqw@qq.com</code>
                    </p>
                </div>
                <div style="margin-bottom: 10px">
                    <label>模式</label>
                    <input type="radio"
                           v-model="settingsFrom.pattern"
                           value="auto">自动模式
                    <input type="radio" v-model="settingsFrom.pattern"
                           value="strict">严格模式
                    <input type="radio" v-model="settingsFrom.pattern"
                           value="high">高并发模式
                    <p style="color: red">严格模式:上传的手机号仅分配给自定的客户端</p>
                </div>
                <div style="margin-bottom: 10px">
                    <label>内容随机因子</label>
                    <input type="radio"
                           v-model="settingsFrom.random"
                           value="true">开启
                    <input type="radio" v-model="settingsFrom.random"
                           value="false">关闭
                    <p style="color: red">开启后在消息文字检'{emoji}'则会插入到指定位置,否则随机插入</p>
                </div>
                <div style="margin-bottom: 10px">
                    <label>随机因子列表</label>
                    <textarea v-model="settingsFrom.emojiList" rows="5" class="large-text code" ></textarea>
                </div>
            </form>
            <form v-if="action=='message'">

                <div style="margin-bottom: 10px">
                    <label>消息</label>
                    <textarea v-model="addsFrom.message" rows="5" class="large-text code" ></textarea>
                    <p v-if="settingsFrom.random" style="color: red">开启后在消息文字检'{emoji}'则会插入到指定位置,否则随机插入</p>
                </div>
            </form>
        </crud-modal>
    </div>
<?php
$js=<<<JS

Vue.component('crud-modal', {
    template: `
<div class="notification-dialog-wrap request-filesystem-credentials-dialog" :style="active ?'display: block;':'display: none;'">
  <div class="notification-dialog-background" @click="modalCancel"></div>
  <div class="notification-dialog" role="dialog">
    <div class="request-filesystem-credentials-dialog-content">
        <div class="request-filesystem-credentials-form">
          <h2>{{title}}</h2>
          <slot></slot>
          <p class="request-filesystem-credentials-action-buttons">
                <button class="button cancel-button" v-if="this.\$listeners['cancel']" type="button" @click="modalCancel">关闭</button>
                <button class="button" type="button" v-if="this.\$listeners['confirm']" @click="modalConfirm">确定</button>
                <button class="button" type="button" v-if="this.\$listeners['submit']"  @click="modalSubmit">提交</button>
          </p>
        </div>
    </div>
  </div>
</div>`,
    data() {
        return {}
    },
    props: {
        title:{
            type: String,
            default: '测试弹窗',
        },
        active:{
            type:Boolean,
            default:false
        },
        delay:{
            type:Number,
            default:0
        }
    },
    watch:{
        delay(){
            if(this.active ==false){
                this.active =true;
                setTimeout(()=>{
                    this.active =false;
                },this.delay)
            }
        },
    },
    computed: {},
    methods: {
        modalCancel(event){
            if( this.\$listeners['cancel']){
                this.\$emit('cancel',event)
            }
        },
        modalConfirm(event){
            if( this.\$listeners['confirm']){
                this.\$emit('confirm',event)
            }
        },
        modalSubmit(event){
            if( this.\$listeners['submit']){
                this.\$emit('submit',event)
            }
        }
    },
});

new Vue({
    el: '.wrap',
    data(){
        return {
            title:"iMessage",
            modalTitle:'',
            activeUrl:'index/message',
            modal:false,
            page:1,
            tableData:[],
            tableTotal: 0,
            pageSize: 10,
            ids:[],
            navTab:{PHP_CODE_submenu},
            action:'add',
            active:false,
            tableFrom:{
                phone:'',
                token:'',
                message:"",
                status:0  
            },
            addsFrom:{
                phone:'',
                message:"",
                token:'',
                status:0
            },
            n:10,
            settings:{},
            token:[],
            settingsFrom:{
                preg:'\\\b\\\d{11}\\\b',
                pattern:'auto',
                random:false,
                emojiList:''
            },
            
        }
    },
    watch:{
        page(value){
            this.init(value)
        },
        n(value){
            console.log(value)
           this.pageSize =parseInt(value) || "all"; 
        },
        pageSize(value){
            this.init(false,value)
        },
    },
    created() {
        // 备份初始数据
        document.querySelector('.wrap').style.display = 'block';
    },
    computed:{
        isChecked(){
            if(typeof this.settingsFrom.pattern == 'string'){
                return ( this.settingsFrom.pattern =="true" ) ? true :false;
            }
           return this.settingsFrom.pattern;
        },
        modStr(){
          if(this.settingsFrom.pattern =='atuo'){
              return "自动模式";
          }else if(this.settingsFrom.pattern =='strict'){
               return "严格模式";
          }else if(this.settingsFrom.pattern =='high'){
              return "高并发模式"; 
          }  
        },
        params(){
            let params ={};
            const urlParams = new URLSearchParams(window.location.search);
            for (const [key, value] of urlParams) {
              params[key] = value;
            }
            return params;
        },
        pageSum(){
            return Math.ceil(this.tableTotal/this.pageSize)||1;
        },
    },
    methods:{
        init(page,pageSize){
            jQuery.ajax({
                url: ajaxurl+"?action=index",
                type: 'GET',
                data: {
                  page: page||this.page,
                  pageSize:pageSize|| this.pageSize
                },
                dataType: 'json',
                success: (res) => {
                    console.log(res)
                  if (res.code == 1) {
                    // 当前页数
                    this.page = parseInt(res.data.page +1);
                    // 总记录数
                    this.tableTotal =parseInt( res.data.total);
                    // 分页大小
                    this.pageSize = parseInt(res.data.pageSize);
                    // 表格数据
                    this.tableData = res.data.tableData;
                    this.settingsFrom = res.data.settings;
                    this.settings = res.data.settings;
                    this.token = res.data.token
                  }
                },
                error: (res) => {
                  console.log(res); 
                }
          })    
        },
        // 新增记录
        add(){
            jQuery.ajax({
                url: ajaxurl+"?action=index",
                type: 'POST',
                data: {
                  type:'add',
                  iMessage:this.tableFrom
                },
                dataType: 'json',
                success: (res) => {
                  if(res.code ==1){
                       this.active =false;
                      alert(res.message);
                  }else {
                      alert(res.message);
                  }
                },
                error: (res) => {
                  console.log(res);
                }
            })
        },
         // 表单提交 批量新增
        adds(){
            const text  = this.addsFrom.phone
            const phoneNumberRegex = new RegExp(this.settingsFrom.preg, 'g')
            const phones = []; 
            let match;
            while ((match = phoneNumberRegex.exec(text)) !== null) {
                phones.push(match[0]);
            }
            const nePhone = [...new Set(phones)];
            if(phones.length != nePhone.length){
                let str = "识别到:"+phones.length + "条数据,其中"+(phones.length - nePhone.length)+"条数据重复\\n"
                const shouldContinue = window.confirm( str+'是否删除重复项操作?');
                if(shouldContinue){
                    this.addsFrom.phone = nePhone.join('\\n')
                }
            } 
            
            jQuery.ajax({
                url: ajaxurl+"?action=index",
                type: 'POST',
                data: {
                  type:'adds',
                  iMessage:this.addsFrom
                },
                dataType: 'json',
                success: (res) => {
                    console.log(res);
                  if(res.code ==1){
                       this.active =false;
                       let total = res.data.total;
                       let success = res.data.success;
                      alert("识别手机号:"+total+"个,保存成功:"+success+"个.");
                      this. init()
                  }else {
                      alert(res.message);
                  }
                },
                error: (res) => {
                  console.log(res);
                }
            }) 
        },
        // 删除记录
        deleteAll(){
            if(this.ids.length<=0){
                alert('请选择记录');
                return;
            }
            jQuery.ajax({
                url: ajaxurl+"?action=index",
                type: 'POST',
                data: {
                  type:'delete',
                  ids:this.ids
                },
                dataType: 'json',
                success: (res) => {
                    alert(res.message)
                    if(res.code ==1){
                        for (const tmp_id of this.ids) {
                            const index = this.tableData.findIndex(item => item.id === tmp_id);
                            if (index !== -1) {
                                this.tableData.splice(index, 1); // 删除特定索引处的元素
                            }
                        }
                    }
                    console.log(res)
                },
                error: (res) => {
                  console.log(res);
                }
            })    
        },
        deleteAllDb(){
            jQuery.ajax({
                url: ajaxurl+"?action=index",
                type: 'POST',
                data: {
                  type:'resetDb',
                },
                dataType: 'json',
                success: (res) => {
                  if(res.code ==1){
                      alert(res.message);
                       this. init()
                  }else {
                      alert(res.message);
                  }
                },
                error: (res) => {
                  console.log(res);
                }
            })
        },
        deleteAllYiFaSong(){
            jQuery.ajax({
                url: ajaxurl+"?action=index",
                type: 'POST',
                data: {
                  type:'deleteAllYiFaSong',
                },
                dataType: 'json',
                success: (res) => {
                  if(res.code ==1){
                      alert(res.message);
                       this. init()
                  }else {
                      alert(res.message);
                  }
                },
                error: (res) => {
                  console.log(res);
                }
            })
        },
        // 表单提交
        submit(){
            if(this.action =='add'){
                  this.add()
            }else if (this.action =='adds'){
                this.adds();
            }else if (this.action =='settings'){
                this.submitSettings();
            }else if (this.action =='message'){
                this.submitMessage();
            }
        },
        submitSettings(){
            jQuery.ajax({
                url: ajaxurl+"?action=index",
                type: 'POST',
                data: {
                  type:'settings',
                  iMessage:this.settingsFrom
                },
                dataType: 'json',
                success: (res) => {
                    console.log(res)
                  if(res.code ==1){
                       this.active =false;
                        this.settings = res.data;
                       this.settingsFrom= res.data;
                  }else {
                      alert(res.message);
                  }
                },
                error: (res) => {
                  console.log(res);
                }
            }) 
        },
        submitMessage(){
            jQuery.ajax({
                url: ajaxurl+"?action=console/cache",
                type: 'POST',
                data: {
                  key:'iMessage_message',
                  value:this.addsFrom.message
                },
                dataType: 'json',
                success: (res) => {
                    console.log(res)
                  if(res.code ==1){
                       alert('设置成功');
                  }else {
                      alert(res.message);
                  }
                },
                error: (res) => {
                  console.log(res);
                }
            }) 
        },
        // 记录选中
        select(id){
           let e =this.ids.indexOf(id);
           if(e ==-1){
               this.ids.push(id);
               
           }else{
               this.ids.splice(e,1)
           }
        },
        // 记录选中全部
        selectAll(){
            if(this.ids.length>0){
                this.ids =[];
            }else{
                 let tmp =[];
                for (let i =0; i<= this.tableData.length-1;i++){
                    tmp.push((this.tableData)[i]['id'])
                }
                this.ids =tmp;
            }
        },
        update(item,index){
           jQuery.ajax({
                url: ajaxurl+"?action=index",
                type: 'POST',
                data: {
                  type:'update',
                  iMessage:item
                },
                dataType: 'json',
                success: (res) => {
                  if(res.code ==1){
                   this.tableData.splice(index,1,item);
                  }else {
                      alert(res.message);
                  }
                },
                error: (res) => {
                  console.log(res);
                }
            }) 
        },
        openAdd(){
            this.modalTitle ='新增'
            this.action='add';
            this.active=true;
        },
        openSettings(){
            this.modalTitle ='设置'
            this.action='settings';
            this.active=true;
        },
        openAdds(){
            this.modalTitle ='批量插入'
            this.action='adds';
            this.active=true;
        },
        statusChange(id){
           for (let index =0;index <= this.tableData.length -1;index++){
               let item = (this.tableData)[index];
               if(item.id ==id){
                   item.status = (item.status ==1) ? 0 : 1;
                   this.update(item,index);
                   return ;
               }
           }
        },
        reset(value){
           jQuery.ajax({
                url: ajaxurl+"?action=index",
                type: 'POST',
                data: {
                  type:'reset',
                  status:value
                },
                dataType: 'json',
                success: (res) => {
                    console.log(res);
                  if(res.code ==1){
                      this.init()
                  }else {
                      alert(res.message);
                  }
                },
                error: (res) => {
                  console.log(res);
                }
            }) 
        },
        test(){
           let arr = [];
           for (let i=0; i<=100;i++){
                let n = "+1"+ (Math.floor(Math.random() * 9000000000) + 1000000000);
               arr.push(n)
           };
           this.addsFrom.phone = arr.join(',');
           this.addsFrom.message='Hello world!';
           this.adds()
        },
        getName(token){
            if((this.token)[token]){
                return (this.token)[token].name;
            }
        },
        setMessage(){
            this.modalTitle ='设置文案'
            this.action='message';
            this.active=true;
        },
    },
    mounted(){
        this. init()
    }
});
JS;
$this->registerJs(php_code_to_js_code(get_defined_vars()));

