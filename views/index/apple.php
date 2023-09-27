<?php
/** @var $this imessage\components\View */
/** @var $modelName string */
/** @var $activeUrl string */
/** @var $columns  string */
/** @var $tmp  string */
/** @var $submenu string */

use imessage\assets\VueAsset;
use imessage\assets\AceAsset;
VueAsset::register($this);
AceAsset::register($this);
wp_enqueue_media();

$js =<<<JS
Vue.component('crud-modal', {
    template: `
<div class="notification-dialog-wrap request-filesystem-credentials-dialog" :style="active ?'display: block;':'display: none;'">
  <div class="notification-dialog-background" @click.stop="modalCancel"></div>
  <div class="notification-dialog" role="dialog">
    <div class="request-filesystem-credentials-dialog-content">
        <div class="request-filesystem-credentials-form">
          <h1>{{title}}</h1>
          <slot></slot>
          <p class="request-filesystem-credentials-action-buttons">
            <button v-if="this.\$listeners['cancel']" class="button cancel-button" data-js-action="close" type="button" @click="modalCancel" >取消</button>
            <button v-if="this.\$listeners['confirm']" class="button" type="button" @click="modalConfirm">确定</button>
            <input v-if="this.\$listeners['submit']" class="button" type="submit" @click="modalSubmit" value="提交">
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


JS;

?>
<div class="wrap" style="display: none">
    <h1 class="wp-heading-inline">{{title}}</h1>
    <hr class="wp-header-end">
    <ul class="nav-tab-wrapper wp-clearfix">
        <a v-for="(url,index) in navTab"
           :href="'/wp-admin/admin.php?page=' +url.url"
           :class="(activeUrl == url.url)?'nav-tab nav-tab-active':'nav-tab'">{{url.text}}</a>
    </ul>

    <!-- 根据根据模版批量字符串 -->
    <div class="tablenav top" style="margin-bottom: 0px">
        <div class="alignleft actions bulkactions">
            <input type="submit" value="新增" class="button action" @click="itemAdd" >
        </div>

        <div class="alignleft actions bulkactions">
            <input type="submit" value="删除" class="button action" @click="itemDelete">
        </div>
        <div class="alignleft actions bulkactions">
            <input type="submit" value="排序" class="button action" @click="my_orderBy">
        </div>
        <div class="alignleft actions bulkactions">
            <input type="submit" value="清空" class="button action" @click="itemDeleteAll">
        </div>
        <div class="alignleft actions bulkactions">
            <input type="submit" value="批量新增" class="button action" @click="clickModal">
        </div>
        <div class="alignleft actions bulkactions">
            <input type="submit" value="设置" class="button action" @click="clickSettings">
        </div>

        <div class="alignleft actions bulkactions">
            <input type="submit" value="控制台" class="button action" @click="()=>{mysqlConsoleActive = !mysqlConsoleActive}">
        </div>
        <div class="alignleft actions bulkactions">
            <input type="submit" value="下载" class="button action" @click="downloadTxt">
        </div>
        <div class="tablenav-pages" style="margin-bottom: 0px">
            <span class="displaying-num">{{total}}条记录</span>
            <select  class="tablenav-pages-navspan button" v-model="pageSize" >
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
                <button :class="'tablenav-pages-navspan button '+((page >1) ?'':'disabled')" @click="up">‹</button>
                <span class="paging-input">
                    第<label for="current-page-selector" class="screen-reader-text">当前页</label>
                    <input  type="text" name="paged" size="2" v-model="page" class="current-page">
                    <span class="tablenav-paging-text">页,共<span class="total-pages">{{pageSum}}</span>页</span>
                </span>
                <button :class="'next-page button '+((page<pageSum)?'':'disabled')" @click="next">›</button>
                <button :class="'tablenav-pages-navspan button ' +((page<pageSum)?'':'disabled') " @click="page =pageSum">»</button>
            </span>
        </div>
    </div>

    <!-- 表哥 -->
    <table class="wp-list-table widefat fixed striped table-view-list " style="margin-top: 5px">
        <!-- 标题 -->
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1" >全选</label>
                    <input id="cb-select-all-1" type="checkbox"  @change="selectAll">
                </td>

                <td class="manage-column"  v-for="th in columns" v-if="fieldsIsShow(th.field)" :style="th.style"><span>{{th.zh_CN}}</span></td>
            </tr>
        </thead>
        <!-- 内容 -->
        <tbody id="the-list">
        <tr  v-for="(value,itemIndex) in table" @dblclick="itemUpdate(value.id)">
            <th scope="row" class="check-column">
                <label class="screen-reader-text" for="cb-select-1">选择</label>
                <input type="checkbox" name="post[]"  @change="selectItem(value.id)">
                <div class="locked-indicator">
                    <span class="locked-indicator-icon" aria-hidden="true"></span>
                </div>
            </th>
            <td v-for="field in columns" v-if="fieldsIsShow(field.field)" class="manage-column">
                    <template  v-if="field.field=='status'">
                        <span :style="'background-color: '+((value[field.field] ==1)?'#52accc':'#cf4944')+';color: white;padding: 5px;border-radius: 5px'"
                          v-html="((value[field.field] ==1)?'启用':'禁用')" @click="changeStatus(value.id,value[field.field])"></span>
                    </template>
                    <span v-else-if="field.field=='created_at'" v-html="formattedDate(value[field.field])"></span>
                    <span v-else-if="field.field=='updated_at'" v-html="formattedDate(value[field.field])"></span>
                    <span v-else v-html="formatted(value[field.field])"></span>
            </td>
        </tr>
        </tbody>
        <!-- 脚 -->
        <tfoot>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"style="" >
                    <label class="screen-reader-text" for="cb-select-all-1" >全选</label>
                    <input id="cb-select-all-1" type="checkbox"  @change="selectAll">
                </td>

                <td class="manage-column" v-for="th in columns" v-if="fieldsIsShow(th.field)" :style="th.style"><span>{{th.zh_CN}}</span></td>
            </tr>
        </tfoot>
    </table>

    <!-- 设置 -->
    <crud-modal :title="modalTitle" :active="active"  @submit="FromSubmit" @cancel="()=>{active=!active}">
        <form v-if="modalType=='from'">
           <p >

           </p>
           <div v-if="tableFromError !=''" class="notice notice-alt notice-error"><p>{{tableFromError}}</p></div>
           <label v-for="(field,key) in tableFrom" for="hostname">
               <span class="field-title">{{field.label}}</span>
               <input :name="key" type="text"  class="code" placeholder="" v-model="tableFrom[key].value" value="">
               <p v-if="tableFrom[key].error !=''" style="margin: 0;color: red;padding: 0;">{{tableFrom[key].error}}</p>
           </label>
        </form>
        <form v-if="modalType=='setting'">
            <p >

            </p>
            <fieldset>
                <legend>显示字段</legend>
                <label for="ftps" v-for="field in columns" >
                    <input type="checkbox" :id="field.field" :value="field.field" v-model="showFields">
                    {{ field.zh_CN }}
                </label>
            </fieldset>
        </form>
    </crud-modal>

    <!-- 批量新增 -->
    <div v-if="templateActive">
        <div tabindex="0" class="media-modal wp-core-ui" role="dialog" aria-labelledby="media-frame-title">
            <div class="media-modal-content" role="document">
                <div class="edit-attachment-frame mode-select hide-menu hide-router">
                    <!-- header -->
                    <div class="edit-media-header">
                        <button class="left dashicons"></button>
                        <button class="right dashicons"></button>
                        <button type="button" class="media-modal-close" @click="clickModal">
                            <span class="media-modal-icon"></span>
                        </button>
                    </div>
                    <div class="media-frame-title"><h1>模版匹配字符串</h1></div>
                    <!-- end header -->
                    <!--  content -->
                    <div class="media-frame-content">
                        <div class="attachment-details save-ready">
                            <div class="attachment-media-view landscape">
                                <div class="thumbnail">
                                    <table class="wp-list-table widefat fixed striped table-view-list">
                                        <!-- 标题 -->
                                        <thead>
                                        <tr>
                                            <td class="manage-column" v-for="th in columns" ><span>{{th.zh_CN}}</span></td>
                                        </tr>
                                        </thead>
                                        <!-- 内容 -->
                                        <tbody id="the-list">
                                        <tr v-for="row in result">
                                            <td v-for="field in columns" v-html="row[field.field]"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <div class="attachment-actions" style="margin-top: 20px">
                                    </div>
                                </div>
                            </div>
                            <div class="attachment-info">
                                <div class="details">
                                    <label  for="hostname">
                                        <span class="field-title">间隔字符串</span>
                                        <input type="text" v-model="intervalStr" placeholder="间隔字符串"   class="large-text code" />
                                    </label>
                                    <label  for="hostname">
                                        <span class="field-title">匹配模版</span>
                                        <textarea v-model="templateStr" placeholder="匹配模版" rows="5" cols="30"  class="large-text code" ></textarea>
                                    </label>
                                    <label  for="hostname">
                                        <span class="field-title">指定其他字段</span>
                                        <button type="button" class="button-link" @click="addField">添加</button>
                                        <button type="button" class="button-link delete-attachment" @click="deleteField">删除</button>
                                        <div >
                                            <div  v-for="(field,index) in installDefaultFields" :key="index" style="display: flex;justify-content: left">
                                                <select class="large-text code" v-model="installDefaultFields[index].key" @change="fieldsChange($event,index,'key')">
                                                    <option v-for="field in columns" v-if="(ignore.indexOf(field.field)!=-1)?false:true" :value="field.field">{{field.zh_CN}}</option>
                                                </select>
                                                <input type="text" class="large-text code" :value="field.value" @input="fieldsChange($event,index,'value')" placeholder="字段值" >
                                            </div>
                                        </div>

                                    </label>
                                </div>

                                <div class="settings">
                                    <label>待匹配字符串</label>
                                    <textarea v-model="activeTemplateStr" rows="15" cols="50"  class="large-text code"></textarea>
                                </div>

                                <div class="actions">
                                    <button type="button" class="button-link" @click="templateSubmit">提交</button>
                                    <span class="links-separator">|</span>
                                    <button type="button" class="button-link" @click="templateTest">匹配</button>
                                    <span class="links-separator">|</span>
                                    <button type="button" class="button-link delete-attachment" @click="templateResetting">重置</button>
                                    <button type="button" class="button-link" @click="templateSubmit">匹配到:{{resultRecord}}条</button>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end content -->
                </div>
            </div>
        </div>
        <div class="media-modal-backdrop"></div>
    </div>
    <!-- mysql  -->
    <div v-show="mysqlConsoleActive">
        <div tabindex="0" class="media-modal wp-core-ui" role="dialog" aria-labelledby="media-frame-title">
            <div class="media-modal-content" role="document">
                <div class="edit-attachment-frame mode-select hide-menu hide-router">
                    <!-- header -->
                    <div class="edit-media-header">

                        <button class="left dashicons"></button>
                        <button class="right dashicons"></button>
                        <button type="button" class="media-modal-close" @click="()=>{mysqlConsoleActive = !mysqlConsoleActive}">
                            <span class="media-modal-icon"></span>
                        </button>
                    </div>
                    <div class="media-frame-title"><h1>Console {{mysqlTableName}}</h1></div>
                    <!-- end header -->
                    <!--  content -->
                    <div class="media-frame-content">
                        <div class="attachment-details save-ready">
                            <div class="attachment-media-view landscape">
                                <div class="thumbnail" style="padding: 0">
                                    <pre id="editor" style="margin: 0;width: 100%;height: 100%"></pre>
                                </div>
                            </div>
                            <div class="attachment-info" style="display: flex;flex-direction: column;padding: 0px;border: 0px">

                                <div class="details" style="flex: 1;padding-bottom: 0px">
                                    <pre id="result" style="margin: 0px; width: 100%; height: 100%"></pre>
                                </div>

                                <div class="settings" v-show="is_settings" style="padding-left:16px;padding-right: 16px ">
                                    <fieldset>
                                        <legend>语言:</legend>
                                        <ul role="list" style="display: flex;flex-wrap: wrap">
                                            <li >
                                                <button v-for="mode in aceModeList"
                                                        type="button" style="margin-bottom: 6px;margin-right: 5px"
                                                        :class="'button button-secondary' +((aceMode==mode) ?' active': '')"
                                                        v-html="getLastPartAndCapitalizeFirstLetter(mode)"
                                                        @click="()=>{aceMode =mode}"
                                                ></button>
                                            </li>
                                        </ul>
                                    </fieldset>
                                    <fieldset>
                                        <legend>主题:</legend>
                                        <select class="regular-text code" v-model="aceTheme" >
                                            <option v-for="theme in aceThemeList"
                                                    :value="theme">{{getLastPartAndCapitalizeFirstLetter(theme)}}</option>
                                        </select>
                                    </fieldset>
                                </div>

                                <div  class="actions" style="padding-left:16px;padding-right: 16px ">
                                    <button type="button" class="button button-link-delete " @click="consoleSubmitAll">
                                        运行全部
                                    </button>

                                    <button type="button" class="button " @click="consoleSubmitOne">
                                        运行一行
                                    </button>
                                    <button type="button" class="button-primary" @click="saveConsole()">保存</button>
                                    <button type="button" class="button" @click="()=>{is_settings =!is_settings}">设置</button>

                               </div>
                            </div>
                        </div>
                    </div>
                    <!-- end content -->
                </div>
            </div>
        </div>
        <div class="media-modal-backdrop"></div>
    </div>
</div>
<?php
$js.=<<<JS
new Vue({
    el: '.wrap',
    data(){
        return {
            initialData: null,
            title:"iMessage",
            modalTitle:'',
            modelName:"{PHP_CODE_modelName}",
            activeUrl:'{PHP_CODE_activeUrl}',
            mysqlTableName:"{PHP_CODE_mysqlTableName}",
            navTab:{PHP_CODE_submenu},
            page:1,
            pageSize: 10,
            table:[],
            total: 0,
            actionType:'add',
            active:false,
            columns:{PHP_CODE_columns},
            modalType:"from",
            tableFrom:{},
            tableFromError:"",
            ids:[],
            actionId:'',
            
            templateActive:false,
            templateStr:"",
            intervalStr:"----",
            activeTemplateStr:"",
            result:[],
            // 显示字段
            showFields:[],
            ignore:['id','created_at','updated_at'],
            installDefaultFields:{PHP_CODE_installDefaultFields},
            orderBy:'id asc',
            mysqlConsoleActive:false,
            editor:{},
            aceThemeList:[
                'ace/theme/monokai','ace/theme/xcode', 'ace/theme/github','ace/theme/dreamweaver',
                 'ace/theme/clouds_midnight','ace/theme/cloud9_night_low_color', 'ace/theme/tomorrow_night',
                 'ace/theme/chrome',
                'ace/theme/tomorrow',
                'ace/theme/cloud9_day',
                'ace/theme/clouds',
                'ace/theme/cobalt',
                'ace/theme/eclipse',
                'ace/theme/nord_dark',
                'ace/theme/dawn',
                'ace/theme/solarized_light',
                'ace/theme/chaos',
                'ace/theme/merbivore_soft',
                'ace/theme/katzenmilch',
                'ace/theme/tomorrow_night_blue',
                'ace/theme/gob',
                'ace/theme/gruvbox',
                'ace/theme/textmate',
                'ace/theme/iplastic',
                'ace/theme/crimson_editor',
                'ace/theme/tomorrow_night_bright',
                'ace/theme/cloud9_night',
                'ace/theme/mono_industrial',
                'ace/theme/merbivore',
                'ace/theme/sqlserver',
                'ace/theme/idle_fingers',
                'ace/theme/gruvbox_light_hard',
                'ace/theme/ambiance',
                'ace/theme/kuroir',
                'ace/theme/pastel_on_dark',
                'ace/theme/gruvbox_dark_hard',
                'ace/theme/kr_theme',
                'ace/theme/twilight',
                'ace/theme/solarized_dark',
                'ace/theme/terminal',
                'ace/theme/dracula',
                'ace/theme/one_dark',
                'ace/theme/vibrant_ink',
                'ace/theme/tomorrow_night_eighties'
            ],
            aceModeList:[ 
                'ace/mode/php','ace/mode/html','ace/mode/sh', 
                'ace/mode/python','ace/mode/jsp','ace/mode/ini',
                'ace/mode/mysql', 'ace/mode/applescript',
                'ace/mode/svg','ace/mode/vbscript',
                'ace/mode/json',
                'ace/mode/text',
                'ace/mode/markdown',
                'ace/mode/javascript',
                'ace/mode/golang',
                'ace/mode/xml','ace/mode/gitignore','ace/mode/sass','ace/mode/nginx'
            ],
            aceTheme:"ace/theme/monokai",
            aceMode:'ace/mode/mysql',
            aceValue:"",
            aceResult:{},
            aceResultTheme:"ace/theme/monokai",
            aceResultMode:'ace/mode/json',
            aceResultValue:{},
            aceFontSize:12,
            cache:{},
            is_settings:false
        }
    },
    watch:{
        showFields(){
            this.templateStrInit()
        },
        activeTemplateStr(newValue){
            if(newValue !=null){
                this.identifyStr(newValue)
            }
        },
        intervalStr(newValue,oldValue){
            if(newValue !='' && oldValue !=''){
                this.templateStr = this.templateStr.replace(new RegExp(oldValue, 'g'), newValue);
            }
        },
        page(value,oldValue){
            if(value != oldValue){
                this.cache.page = value
                this.init()
            }
            
        },
        pageSize(value){
            this.cache.pageSize = value
            this.init(this.page,value,'',this.orderBy)
        },
        aceMode(newValue){
          this.editor.session.setMode(newValue)
        },
        aceTheme(newValue){
           this.editor.setTheme(newValue);
           this.aceResult.setTheme(newValue)
        },
        aceFontSize(){
             this.editor.setFontSize(newValue+"px");
        },
        aceResultValue(){
             this.aceResult.setValue(JSON.stringify( this.aceResultValue,null,2));
             this.aceResult.moveCursorToPosition({ row: 0, column: 0 });
        }
    },
    created() {
        // 备份初始数据
        document.querySelector('.wrap').style.display = 'block';
        this.initialData = this.\$data;
    },
    computed:{
        resultRecord(){
          return this.result.length;  
        },
        // 缓存名称
        localStorage_name(){
            return this.modelName+"showFields"
        },
        pageSum(){
            return Math.ceil(this.total/this.pageSize)||1;
        },
        // 额外字段
        defaultFields(){
            let obj ={};
            for (let i = 0;i <= (this.installDefaultFields.length-1);i++){
                let key = (this.installDefaultFields)[i].key
                let value = (this.installDefaultFields)[i].value
                if(key != '' && value !='' && this.ignore.indexOf(key) ==-1){
                    obj[key]=value
                }
            }
            return obj;
        },
    },
    methods:{
       next(){
             if(this.page <this.pageSum){
                this.page =  parseInt(this.page)+1
            }
        },
       up(){
           if(this.page <=this.pageSum && this.page>1){
            this.page =  parseInt(this.page)-1
           }
        },
       // 匹配模版初始化
       templateStrInit(){
            let tmp =[];
            for (let i =0;i<=this.columns.length-1;i++){
                if(this.ignore.indexOf((this.columns)[i].field) ==-1){
                    if(this.showFields ==0){
                        tmp.push("{"+(this.columns)[i].field +"}")
                    }else{
                        if(this.showFields.indexOf((this.columns)[i].field) >=0){
                            tmp.push("{"+(this.columns)[i].field +"}")
                        }
                    }
                }
            }
            this.templateStr = tmp.join(this.intervalStr);
       },
       init(page,pageSize,where,orderBy){
           jQuery.ajax({
                url: ajaxurl+"?action="+this.activeUrl,
                type: 'GET',
                dataType: 'json',
                data: {
                  page: page||this.page,
                  pageSize:pageSize|| this.pageSize,
                  orderBy:orderBy||this.orderBy,
                  //where:this.selectField +  '' + where||this.selectWhere
               
                },
                success: (res) => {
                    console.log(res);
                    if(res.code ==1){
                        //this.page = parseInt(res.data.page +1)
                        //this.pageSize = parseInt(res.data.pageSize)
                        this.table = res.data.table
                        this.total = res.data.total
                    }
                },
                error: (res) => {
                  console.log(res);
                }
            }) 
       },
       // 编辑器
       aceInit(){
            this.editor = window.ace.edit('editor');
            this.editor.setTheme(this.aceTheme);
            let sql = this.sqlExample()
            this.editor.setValue( sql);
            this.editor.setFontSize(this.aceFontSize +"px");
            this.editor.session.setMode(this.aceMode);
            window.onload = ()=> {  
                this.editor.resize();
            }
            this.editor.moveCursorToPosition({ row: 0, column: 0 });
       },
       aceResultInit(){
           let sql = JSON.stringify({"DA":"DA"},null,4)
           this.aceResult = window.ace.edit('result');
           this.aceResult.setTheme(this.aceResultTheme);
           this.aceResult.session.setMode(this.aceResultMode);
           this.aceResult.setFontSize("12px");
           this.aceResult.setValue("");
           window.onload = ()=> {  
                this.aceResult.resize();
            }
           this.aceResult.moveCursorToPosition({ row: 0, column: 0 });
       },
       // 新增
       itemAdd(){
           this.actionType ='add'
           this.modalTitle ="新增"
           this.modalType="from" 
           let ignore = this.ignore;
           let from ={};
           for(let i=0;i<=this.columns.length -1;i++){
               let item = (this.columns)[i]
               if(ignore.indexOf(item.field) ==-1){
                   from[item.field]={'label':item.zh_CN,value:'',error:''}
               }
           }
           this.tableFrom=from
           this.active =true;
       },
       formSerialize(){
           let fromData={};
           for (let i in this.tableFrom){
               fromData[i]= (this.tableFrom)[i].value
           }
           return fromData
       },
       formSerializeError(error){
           for (let i in error){
               if((this.tableFrom)[i] != undefined){
                    if( error[i] instanceof Array){
                      (this.tableFrom)[i].error=error[i].join('.')
                    }
               }
           }
          
       },
       // 删除
       itemDelete(){
           jQuery.ajax({
                url: ajaxurl+"?action="+this.activeUrl,
                type: 'DELETE',
                data:{ids:this.ids},
                dataType: 'json',
                success: (res) => {
                    console.log(res)
                    if(res.code ==1){
                        for(let id in this.ids){
                            const index = this.table.findIndex(element => element.id === id); 
                            this.table.splice(index,1)
                            this.ids =[]
                        }
                    }
                },
                error: (res) => {}
            }) 
       },
       // 清空
       itemDeleteAll(){
           if (confirm('确定要清空所有数据吗？')){
                jQuery.ajax({
                    url: ajaxurl+"?action="+this.activeUrl,
                    type: 'PUT',
                    dataType: 'json',
                    success: (res) => {
                        if(res.code ==1){
                            alert(res.message)
                            Object.assign(this.\$data, this.initialData);
                        }
                    },
                    error: (res) => {
                      console.log(res);
                    }
                }) 
           }
          
       },
       // 修改
       itemUpdate(id){
           if(id instanceof PointerEvent){
               console.log("执行1")
               let ids = this.ids;
               if(ids.length >0){
                   for(let i =0;i<ids.length-1;i++){
                       this.itemUpdate(ids[i])
                   }
               }
              
           }else{
               console.log("执行2")
                this.actionType ='update'
                this.actionId= id
                this.modalTitle ="修改 ID:"+id
                this.modalType="from" 
                let ignore = this.ignore;;
                let from ={};
                const index = this.table.findIndex(element => element.id === id); 
                if(index >=0){
                    let tableItem = (this.table)[index];
                    for(let i=0;i<=this.columns.length -1;i++){
                        let item = (this.columns)[i]
                        if(ignore.indexOf(item.field) ==-1){
                           from[item.field]={'label':item.zh_CN,value: tableItem[item.field],error:''}
                        }
                    }
                    this.tableFrom=from
                    this.active =true;
                }
               
           }
            return;
       },
       FromSubmit(){
           let data ={};
            console.log(this.actionType)
           if(this.actionType =='add'){
               data[this.modelName] = this.formSerialize();
               jQuery.ajax({
                    url: ajaxurl+"?action="+this.activeUrl,
                    type: 'POST',
                    data:data,
                    dataType: 'json',
                    success: (res) => {
                        console.log(res)
                        if(res.code ==1){
                            this.active= false
                            this.table.push(res.data)
                        }else {
                            this.formSerializeError(res.data)
                        }
                        this.actionType=''
                    },
                    error: (res) => {
                      console.log(res);
                    }
               }) 
           }
           if(this.actionType =='update'){
               data[this.modelName] = this.formSerialize();
               jQuery.ajax({
                    url: ajaxurl+"?action="+this.activeUrl +"&id="+this.actionId,
                    type: 'PATCH',
                    data:data,
                    dataType: 'json',
                    success: (res) => {
                        if(res.code ==1){
                             const index = this.table.findIndex(element => element.id === this.actionId); 
                             this.table.splice(index,1,res.data)
                             this.active= false
                        }else {
                            this.formSerializeError(res.data)
                        }
                        this.actionType=''
                    },
                    error: (res) => {
                      console.log(res);
                    }
               }) 
           }
           if(this.actionType =='setting'){
                localStorage.setItem(this.localStorage_name, JSON.stringify(this.showFields));
                this.active= false
                this.modalType="from"  
                this.\$forceUpdate() 
           }
       },
       FromConfirm(){
           
       },
       // 全选
       selectAll(){
           if(this.ids.length ==0){
               for (let i =0;i<= this.table.length-1;i++){
                   this.ids.push((this.table)[i].id)
               }
           }else {
               this.ids =[]
           }
       },
       // 单选
       selectItem(id){
           if(this.ids.indexOf(id) ==-1){
               this.ids.push(id)
           }else {
               this.ids.splice(this.ids.indexOf(id),1)
           }
       },
       // 修改状态
       changeStatus(id,Status){
           let data ={};
           const index = this.table.findIndex(element => element.id === id);
           Status = (Status ==0) ? 1 : 0;

           data[this.modelName] = {'status':Status};
           jQuery.ajax({
                url: ajaxurl+"?action="+this.activeUrl +"&id="+id,
                type: 'PATCH',
                data:data,
                dataType: 'json',
                success: (res) => {
                    if(res.code ==1){
                         this.table.splice(index,1,res.data)
                    }
                },
                error: (res) => {
                  console.log(res);
                }
           }) 
       },
       // 匹配
       clickModal(){
           this.templateActive = ! this.templateActive
       },
       // 显示字段
       clickSettings(){
            this.modalType="setting" 
            this.actionType ='setting'
            this.modalTitle ="显示字段"
            this.active =true;
       },
       // 排序
       my_orderBy(){
         if(this.orderBy=='id desc'){
             this.orderBy="id asc"
             this.init(this.page,this.pageSize,false,"id asc")
         }else {
              this.orderBy="id desc"
              this.init(this.page,this.pageSize,false,"id desc")
         } 
       },
       rexStr(template,str){
           const pattern = /{(\w+)}/g;
           let match;
            const result = {};
            while ((match = pattern.exec(template))) {
                console.log(match.index, pattern.lastIndex)
              const key = match[1];
               
              const value = str.substring(match.index, pattern.lastIndex-2).trim();
               console.log(key,value)
              result[key] = value;
            }
           return result;
       },
       // 判断哪些字段显示
       fieldsIsShow(field){
           if(this.showFields.length >0){
               if(this.showFields.indexOf(field) ==-1){
                   return false
               }
           }
           return true
           
       },
       // 时间戳格式化
       formattedDate(timestamp) {
            const date = new Date(timestamp *1000);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');
            return `\${year}-\${month}-\${day} \${hours}:\${minutes}:\${seconds}`;
        },
       formatted(value){
           if(value != null){
            const email = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const url = /^(http|https):\/\//;
            if( email.test(value.toString())){
                 return `<a href="mailto:\${value}" target="_blank">\${value}</a>`
            }
            if( url.test(value.toString())){
                let domain =''
                const domainRegex = /(?:https?:\/\/)?([^/]+)(?:\/.*)?/;
               
                const match = value.toString().match(domainRegex);
                  if (match && match[1]) {
                    domain= match[1];
                  }
                return `<a href="\${value}" target="_blank">\${domain}</a>`
            }
            return value.toString()   
           }
            
       },
       // 匹配字符串
       identifyStr(str){
          let arr = str.split('\\n');
          let result =[];
          if(arr.length>0){
              const regex = /{([^}]*)}/g;
              const matches = this.templateStr.toString().match(regex).map(match => match.slice(1, -1));
              if(matches.length>0){
                  for (let i=0;i<=arr.length-1;i++){
                      if(arr[i] !=null && arr[i]!=""){
                          let row  = arr[i].toString().split(this.intervalStr.toString())
                          if(row.length ==matches.length){
                              result.push(
                                  Object.assign(
                                      {}, 
                                      matches.reduce(
                                          (obj, key, index) => {
                                              obj[key] = row[index];
                                              return obj;
                                            }, {}
                                      ), 
                                      this.defaultFields
                                  )
                              )
                          }  
                      }
                      
                  }
              }
          }
          console.log(this.defaultFields)
          this.result =result;
       },
       // 重置匹配模版
       templateResetting(){
            this.templateStr=""
            this.intervalStr="--"
            this.activeTemplateStr=""
            this.result=[]
       },
       // 匹配新增
       templateSubmit(){
           let data={};
           if(this.result.length>0){
               data[this.modelName] = this.result;
               jQuery.ajax({
                    url: ajaxurl+"?action="+this.activeUrl,
                    type: 'POST',
                    data:data,
                    dataType: 'json',
                    success: (res) => {
                        console.log(res)
                         if(res.code ==1){
                             alert("成功："+res.data.success + " 失败:" +res.data.error)
                             this.result=[]
                             //this.templateResetting()
                        }
                    },
                    error: (res) => {
                      console.log(res);
                    }
               }) 
           }
       },
       // 匹配测试
       templateTest(){
           this.identifyStr(this.activeTemplateStr)
       },
       // 监听字段值变化
       fieldsChange(event,index,fieldType){
           let item = (this.installDefaultFields)[index];
           let value = event.target.value;
           if(fieldType=='key'){
               item.key = value
           }else {
               item.value = value
           }
           this.installDefaultFields.splice(index,1,item)
           
       },
       // 填写字段
       addField(){
           if(this.installDefaultFields.length <= this.columns.length){
                let index = this.installDefaultFields.length +1
                this.installDefaultFields.push({'key':'','value':'字段'+index})
           }
       },
       // 删除字段
       deleteField(){
           if(this.installDefaultFields.length <= this.columns.length && this.installDefaultFields.length >1){
              
                this.installDefaultFields.splice(this.installDefaultFields.length-1,1)
           }else if(this.installDefaultFields.length ==1){
                 this.installDefaultFields =[]
           }
       },
       // 下载
       downloadTxt(){
           const tabStr = this.arrayToTxt(this.table);
           if(tabStr){
               const blob = new Blob([tabStr], { type: "text/plain" });
               const url = URL.createObjectURL(blob);
              const link = document.createElement("a");
              link.href = url;
              link.download = "data.txt"; 
              link.click();
              URL.revokeObjectURL(url);
           }
          
           
       },
       arrayToTxt(){
           let result =[]
           let type =(this.showFields.length ==0)
           // 表头
           let header_arr = [];
           if(type){
               for (let h=0;h<=this.columns.length-1; h++){
                    header_arr.push((this.columns)[h].zh_CN)
               }
           }else{
                for (let h=0;h<=this.columns.length-1; h++){
                    if(this.showFields.indexOf((this.columns)[h].field)!= -1){
                         header_arr.push((this.columns)[h].zh_CN)
                    }
               }
           }
           result.push(header_arr)
           for(let row =0;row <= this.table.length-1;row++){
               let arr = [];
               
               for (let f=0;f<=this.columns.length-1; f++){
                   if(type){
                       arr.push((this.table)[row][(this.columns)[f].field])
                   }else {
                        let index  = this.showFields.indexOf((this.columns)[f].field)
                        if(index != -1){
                            arr.push((this.table)[row][(this.columns)[f].field])
                        }
                   }
               }
               
               result.push(arr)
           }
           return  result.map(obj => Object.values(obj).join(this.intervalStr)).join('\\n');
       },
       // 查询控制台
       // 分割字符串
       getLastPartAndCapitalizeFirstLetter(str) {
            const parts = str.split("/");
            const lastPart = parts[parts.length - 1];

            // 将最后一个部分的下划线替换为空格
            const replacedLastPart = lastPart.replace(/_/g, ' ');
        
            // 将每个单词的首字母大写
            return  replacedLastPart.replace(/\b\w/g, match => match.toUpperCase());

            
        },
       consoleSubmitAll(){
           jQuery.ajax({
                url: ajaxurl+"?action=console/select",
                type: 'POST',
                dataType: 'json',
                data: {
                  'sql':this.editor.getValue()
                },
                success: (res) => {
                   
                    this.aceResultValue=res
                },
                error: (res) => {
                  this.showResult(res)
                }
            }) 

       },
       consoleSubmitOne(){
            const { row } = this.editor.getCursorPosition();
            const lineText = this.editor.session.getLine(row);
            if(lineText !='' && lineText != undefined){
                jQuery.ajax({
                    url: ajaxurl+"?action=console/select",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                      'sql':lineText
                    },
                    success: (res) => {
                        if(res.code ==1){
                            this.editor.moveCursorToPosition({ row: row+1, column: 0 });
                        }
                        this.aceResultValue=res
                    },
                    error: (res) => {
                      this.showResult(res)
                    }
                }) 
            }
       },
       showResult(result_str){
           this.\$refs.result.innerHTML = '';
           const preElement = document.createElement('pre');
           preElement.setAttribute('id', 'result');
           preElement.style.margin = '0';
           preElement.style.width = '100%';
           preElement.style.height = `\${this.calculateHeight(result_str)}px`;
           // 将新创建的span元素挂载到Vue组件的DOM中
           this.\$refs.result.appendChild(preElement);
           const resultEditor = window.ace.edit('result');
           resultEditor.setTheme(this.aceTheme);
           resultEditor.session.setMode('ace/mode/json');
           resultEditor.setFontSize('12px');
           resultEditor.setValue(result_str);
           resultEditor.moveCursorToPosition({ row: 0, column: 0 });
       },
       calculateHeight(text,lineHeight=16) {
            const lines = text.split('\\n');
            return (lines.length+2) * lineHeight;
       },
       saveConsole(){
         this.cache.aceValue = this. editor.getValue();
         this.cacheSave()
       },
       cacheInit(){
            let cache_name = this.activeUrl+"-cache" 
            jQuery.ajax({
                url: ajaxurl+"?action=console/cache",
                type: 'GET',
                dataType: 'json',
                data: {key:cache_name},
                success: (res) => {
                    console.log(res)
                    if(res.code ==1){
                        this.aceValue = res.data.aceValue || this.aceValue
                        this.page = res.data.page || this.page
                        this.pageSize = res.data.pageSize || this.pageSize
                        this.cache = res.data
                    }
                },
                error: (res) => {
                  console.log(res);
                }
            }) 
       },
       cacheSave(){
           let cache_name = this.activeUrl+"-cache" 
           console.log(this.cache)
            jQuery.ajax({
                url: ajaxurl+"?action=console/cache",
                type: 'POST',
                dataType: 'json',
                data: {key:cache_name,value:this.cache},
                success: (res) => {
                    console.log(res)
                    if(res.code ==1){
                       //this.cache = res.data
                    }
                },
                error: (res) => {
                  console.log(res);
                }
            }) 
       },
       sqlExample(){
         const fields = this.columns.map(obj => obj.field);
         const table_name = this.mysqlTableName;
         const s = fields.filter(item => !this.ignore.includes(item))
         const fields_str = s.join(', ')
         const fields_str1 = s.join('\', \'')   
         const field1 = s[Math.floor(Math.random() * s.length)];
         let sql =[
             
             `/**
 * +---------------------------------------------------------------------
 * | Mysql Example 
 * +---------------------------------------------------------------------
 */`,
              '-- 查询',
             `SELECT \${fields_str} FROM \${table_name};`,
              '-- 修改',
             `UPDATE \${table_name} SET \${field1} = 35 WHERE id = 2;`,
              '-- 删除',
             `DELETE FROM \${table_name} WHERE id = 1;`,
              '-- 新增',
             `INSERT INTO \${table_name} (\${fields_str}) VALUES \\n ('\${fields_str1}');`,
             '-- 重置主键',
             `ALTER TABLE \${table_name} AUTO_INCREMENT = 1`,
             '-- 统计记录条数',
              `SELECT COUNT(*) AS total FROM \${table_name}; `,
              '-- 获取字段不重复的所有值',
              `SELECT DISTINCT \${field1} FROM \${table_name};`,
               '-- 升序排序',
              `SELECT * FROM \${table_name} ORDER BY \${field1} ASC;`,
               '-- 降序排序',
              `SELECT * FROM \${table_name} ORDER BY \${field1} DESC;`,
               '-- 前10条记录',
              `SELECT * FROM \${table_name} LIMIT 10;`,
               '-- 统计表中不通字段值出现的次数',
              `SELECT \${field1}, COUNT(*) AS count FROM \${table_name} GROUP BY \${field1};`,
              '-- 添加新字段',
              `ALTER TABLE \${table_name} ADD 字段名称 VARCHAR(100) COMMENT '注释';`,
              `ALTER TABLE \${table_name} ADD 字段名称 VARCHAR(100) COMMENT '注释' AFTER \${field1};`,
               '-- 添加新字段',
              `ALTER TABLE \${table_name} DROP \${field1};`,
               '',
               '-- 查询表定义',
              `SHOW CREATE TABLE \${table_name};`,
              '-- 查询表的索引信息',
              `SHOW INDEXES FROM \${table_name};`,
              '-- 查询表的列信息',
              `SHOW COLUMNS FROM \${table_name};`,
              '-- 函数:如果第一个参数不为NULL，则返回第一个参数,否则返回第二个参数',
              `SELECT IFNULL(salary, 0) FROM \${table_name};`,
              '-- 字段名称和注释',
              `SELECT COLUMN_NAME as field, COLUMN_COMMENT as label FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'wordpress' AND TABLE_NAME = '\${table_name}';`,
              '',
              `SHOW TABLES;             -- 查询数据库所有表`,
              `SELECT UUID();           -- 生成 UUID`,
              `SELECT VERSION();        -- 查询数据库版本信息`,
              `SELECT NOW();            -- 返回当前日期和时间`,
              `SELECT DATE(NOW());      -- 提取日期部分`,
              `SELECT TIME(NOW());      -- 提取时间部分`,
              `SELECT UNIX_TIMESTAMP(); -- 查询当前的Unix时间戳`,
              `SHOW TABLE STATUS FROM wordpress;                -- 数据库的大小`,
              `SELECT DATE_ADD('2023-07-25', INTERVAL 3 DAY);   -- 对日期进行加减运算 `,
              `SELECT DATE_SUB('2023-07-25', INTERVAL 1 WEEK);  -- 对日期进行加减运算 `,
             
             
         ];
         return sql.join('\\n')
         
           
       },
    },
    mounted(){
        this.cacheInit()
        this.init()
        const cachedShowFields = localStorage.getItem(this.localStorage_name);
        if (cachedShowFields) {
             this.showFields=  JSON.parse(cachedShowFields);
        }else {
             this.showFields=  []
        }
        this.templateStrInit()
        this.aceInit()
        this.aceResultInit()
    }
});

JS;
$this->registerJs(php_code_to_js_code(get_defined_vars()));
