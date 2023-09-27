<?php
/** @var $this yii\web\View */

use imessage\assets\VueAsset;
use imessage\assets\AceAsset;
VueAsset::register($this);
AceAsset::register($this);
wp_enqueue_media();
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
                <input type="submit" value="新增" class="button action" @click="add" >
            </div>

            <div class="alignleft actions bulkactions">
                <input type="submit" value="删除" class="button action" @click="deleteAll">
            </div>
            <div class="alignleft actions bulkactions">
                <input type="submit" value="提交" class="button action" @click="submit">
            </div>
        </div>

        <!-- 根据根据模版批量字符串 -->
        <table class="wp-list-table widefat fixed striped table-view-list " style="margin-top: 5px">
            <!-- 标题 -->
            <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1" >全选</label>
                    <input id="cb-select-all-1" type="checkbox"  @change="selectAll">
                </td>
                <td class="manage-column"  style="width: 80px"><span>序号</span></td>
                <td class="manage-column" ><span>名称</span></td>
                <td class="manage-column" ><span>类型</span></td>
                <td class="manage-column" ><span>内容</span></td>
                <td class="manage-column" ><span>Ip</span></td>
                <td class="manage-column" ><span>创建时间</span></td>
                <td class="manage-column"  ><span>修改时间</span></td>
            </tr>
            </thead>
            <!-- 内容 -->
            <tbody id="the-list">
                <tr v-for="(item,index) in  bookList" :key="item.id" @dblclick="itemUpdate(item.id)">
                    <th scope="row" class="check-column">
                        <label class="screen-reader-text" for="cb-select-1">选择</label>
                        <input type="checkbox" name="post[]"  @change="selectItem(item.id)">
                        <div class="locked-indicator">
                            <span class="locked-indicator-icon" aria-hidden="true"></span>
                        </div>
                    </th>
                    <td  class="manage-column">
                        <span>{{item.id}}</span>
                    </td>
                    <td  class="manage-column">
                        <span>{{item.name}}</span>
                    </td>
                    <td  class="manage-column">
                        <span>{{item.type}}</span>
                    </td>
                    <td  class="manage-column">
                        <span>{{getBookStr(item.value,8)}}</span>
                    </td>
                    <td  class="manage-column">
                        <span>{{item.ip}}</span>
                    </td>
                    <td  class="manage-column">
                        <span>{{item.created_at}}</span>
                    </td>
                    <td  class="manage-column">
                        <span>{{item.updated_at}}</span>
                    </td>
                </tr>
            </tbody>
        </table>


        <div v-show="active">
            <div tabindex="0" class="media-modal wp-core-ui" role="dialog" aria-labelledby="media-frame-title">
                <div class="media-modal-content" role="document">
                    <div class="edit-attachment-frame mode-select hide-menu hide-router">
                        <!-- header -->
                        <div class="edit-media-header">

                            <button class="left dashicons"></button>
                            <button class="right dashicons"></button>
                            <button type="button" class="media-modal-close" @click="()=>{active = !active}">
                                <span class="media-modal-icon"></span>
                            </button>
                        </div>
                        <div class="media-frame-title"><h1>{{activeBook.name}}</h1></div>
                        <!-- end header -->
                        <!--  content -->
                        <div class="media-frame-content">
                            <div class="attachment-details save-ready">
                                <div class="attachment-media-view landscape">
                                    <div class="thumbnail" style="padding: 0">
                                        <pre id="editor" style="margin: 0;width: 100%;height: 100%"></pre>
                                    </div>
                                </div>
                                <div class="attachment-info" >

                                    <div class="details" >
                                       <div>
                                           <strong>序号: </strong> {{activeBook.id}}
                                       </div>
                                        <div>
                                            <strong>名称: </strong> {{activeBook.name}}
                                        </div>
                                        <div>
                                            <strong>类型: </strong> {{activeBook.type}}
                                        </div>
                                        <div>
                                            <strong>Ip: </strong> {{activeBook.ip}}
                                        </div>
                                        <div>
                                            <strong>创建时间: </strong> {{activeBook.created_at}}
                                        </div>
                                        <div>
                                            <strong>修改时间: </strong> {{activeBook.updated_at}}
                                        </div>
                                    </div>

                                    <div class="settings" >
                                        <fieldset>
                                            <legend>语言:</legend>
                                            <ul role="list" style="display: flex;flex-wrap: wrap">
                                                <li >
                                                    <button v-for="mode in aceModeList"
                                                            type="button" style="margin-bottom: 6px;margin-right: 5px"
                                                            :class="'button  button-small' +((activeBook.type==firstLetter(mode)) ?' button-disabled': '')"
                                                            v-html="firstLetter(mode)"
                                                            @click="changeMode(mode)"
                                                    ></button>
                                                </li>
                                            </ul>
                                        </fieldset>

                                        <fieldset>
                                            <legend>主题:</legend>
                                            <ul role="list" style="display: flex;flex-wrap: wrap">
                                                <li >
                                                    <button v-for="mode in aceThemeList"
                                                            type="button" style="margin-bottom: 6px;margin-right: 5px"
                                                            :class="'button ' +((editorTheme==mode) ?' button-disabled': '')"
                                                            v-html="firstLetter(mode)"
                                                            @click="changeTheme(mode)"
                                                    ></button>
                                                </li>
                                            </ul>
                                        </fieldset>
                                    </div>

                                    <div  class="actions" >
                                        <button type="button" class="button-primary" @click="bookSave">保存</button>
                                        <button type="button" class="button button-cancel" @click="()=>{active = !active}">取消</button>
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
$js=<<<JS
new Vue({
    el: '.wrap',
    data(){
        return {
            title:"iMessage",
            activeUrl:'{PHP_CODE_activeUrl}',
            navTab:{PHP_CODE_submenu},
            bookList:[],
            ids:[],
            cacheName:'imessage_book',
            active:false,
            activeBook:{},
            aceModeList:[ 
                'ace/mode/php',
                'ace/mode/html',
                'ace/mode/javascript',
                'ace/mode/css',
                'ace/mode/c_cpp',
                'ace/mode/csharp',
                'ace/mode/java',
                'ace/mode/python',
                'ace/mode/golang',
                'ace/mode/mysql', 
                'ace/mode/sh', 
                'ace/mode/powershell',
                'ace/mode/applescript',
                'ace/mode/vbscript',
                'ace/mode/svg',
                'ace/mode/json',
                'ace/mode/text',
                'ace/mode/markdown',
                'ace/mode/xml',
                'ace/mode/ini',
                'ace/mode/dockerfile',
                'ace/mode/gitignore',
                'ace/mode/sass',
                'ace/mode/nginx',
                'ace/mode/robot',
                
            ],
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
            editor:{},
            editorTheme:"ace/theme/monokai"
        }
    },
    watch:{
        
    },
    created() {
        // 备份初始数据
        document.querySelector('.wrap').style.display = 'block';
    },
    computed:{
        
    },
    methods:{
        init(){
           jQuery.ajax({
                url: ajaxurl+"?action=console/cache",
                type: 'GET',
                dataType: 'json',
                data: {key:this.cacheName},
                success: (res) => {
                    console.table(res)
                    if(res.code ==1){
                         let list =res.data?JSON.parse( decodeURIComponent(window.atob(res.data))) : [];
                        if(Array.isArray(list)){
                             this.bookList =list
                             console.log('arr')
                        }
                    }
                },
                error: (res) => {
                  console.log(res);
                }
            }) 
        },
        aceInit(){
            this.editor = window.ace.edit('editor');
            this.editor.setTheme(this.editorTheme);
            this.editor.setFontSize("16px");
            this.editor.session.setMode('ace/mode/applescript');
            this.editor.getSession().on('change', () =>{
                this.activeBook.value  = this.editor.getValue();    
            });
            window.onload = ()=> {  
                this.editor.resize();
            }
            this.editor.moveCursorToPosition({ row: 0});
        },
        async add(){
            let name = window.prompt("请填写文件名称","defaultText");
            let type = window.prompt("请填写类型","Text"); 
            let time = this.getTime()
            let bookItem ={
                id:this.bookList.length+1,
                name:name,
                type:type,
                ip:await this.getIp(),
                value:'',
                created_at:time,
                updated_at:time
            }
            this.bookList.push(bookItem)
            console.table(this.bookList)
        },
        getTime(){
            const date = new Date();
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');
            return `\${year}-\${month}-\${day} \${hours}:\${minutes}:\${seconds}`;
        },
        selectAll(){
            
        },
        getBookStr(str,n){
            str = str.replace(/\\n/g, '')
            let str_length =str.length
            if(n<=str_length){
                return str.slice(0, n)+"...";
            }
            return  str;
        },
        selectItem(id){
            
        },
        submit(){
            jQuery.ajax({
                url: ajaxurl+"?action=console/cache",
                type: 'POST',
                dataType: 'json',
                data: {
                    key:this.cacheName,
                    value: window.btoa(encodeURIComponent(JSON.stringify( this.bookList)))|| []
                },
                success: (res) => {
                    if(res.code ==1){
                        alert('提交成功')
                    }
                },
                error: (res) => {
                  console.log(res);
                }
            }) 
        },
        // 双击出发
        itemUpdate(id){
            let item = this.bookList.find(item => item.id === id);
            this.activeBook = item
            if(item.type != null && item.type!=undefined){
                 let mode = 'ace/mode/'+item.type.toLowerCase()
                 if(this.aceModeList.indexOf(mode) !== -1){
                    this.editor.session.setMode(mode)
                 }
            }
            this.editor.setValue( item.value);
            this.editor.moveCursorToPosition({ row: 0});
            this.active=true
        },
        firstLetter(str) {
            const parts = str.split("/");
            const lastPart = parts[parts.length - 1];
            // 将最后一个部分的下划线替换为空格
            const replacedLastPart = lastPart.replace(/_/g, ' ');
            // 将每个单词的首字母大写
            return  replacedLastPart.replace(/\b\w/g, match => match.toUpperCase())
        },
        async getIp(){
            return new Promise((resolve, reject) => {
                jQuery.ajax({
                url: ajaxurl+"?action=console/ip",
                type: 'GET',
                dataType: 'json',
                success: (res) => {
                    if(res.code ==1){
                         resolve(res.data.ip)
                    }
                },
                error: (res) => {
                  reject(res);
                }
            }) 
            })
        },
        // 全选
        selectAll(){
           if(this.ids.length ==0){
               for (let i =0;i<= this.bookList.length-1;i++){
                   this.ids.push((this.bookList)[i].id)
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
        deleteAll(){
          for (let i =1 ;i<=this.ids.length;i++){
            let index = this.bookList.findIndex(item => item.id === (this.ids)[i]);
            this.bookList.splice(index,1)
          }
        },
        async bookSave(){
            let index = this.bookList.findIndex(item => item.id === this.activeBook.id);
            this.activeBook.updated_at = this.getTime()
            this.activeBook.ip = await this.getIp()
            this.bookList.splice(index,1,this.activeBook)
            this.activeBook={}
            this.active=false
        },
        changeMode(mode){
            this.activeBook.type =this.firstLetter(mode)
             this.editor.session.setMode(mode);
        },
        changeTheme(Theme){
            this.editorTheme =Theme
            this.editor.setTheme(Theme);
        }
    },
    mounted(){
        this.init();
        this.aceInit()
    }
});

JS;
$this->registerJs(php_code_to_js_code(get_defined_vars()));