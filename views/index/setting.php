<?php
/** @var $this yii\web\View */

use imessage\assets\VueAsset;
$settings  = json_encode( Yii::$app->params["settings"]);
VueAsset::register($this);
?>
    <div class="wrap" style="display: none">
        <h1 class="wp-heading-inline">{{title}}</h1>
        <hr class="wp-header-end">
        <ul class="nav-tab-wrapper wp-clearfix">
            <a v-for="(url,index) in navTab"
               :href="'/wp-admin/admin.php?page=' +url.url"
               :class="(activeUrl == url.url)?'nav-tab nav-tab-active':'nav-tab'">{{url.text}}</a>
        </ul>
        <?php
        settings_errors();
        ?>
        <div id="nav-menus-frame" class="wp-clearfix" >
            <div id="menu-settings-column" class="metabox-holder">
                <div class="clear"></div>
                <h2>Sections</h2>
                <div id="side-sortables" class="accordion-container">
                    <ul class="outer-border">
                        <li v-for="(setting,key) in settings"
                            :class="'control-section accordion-section add-post-type-page'+(activeSetting==key?' open':'')"
                            @click="activeSetting =key"
                            id="add-post-type-page">
                            <h3 class="accordion-section-title hndle" style="padding: 10px 10px 11px 14px">
                                {{FirstLetter(setting.section_id)}}	<span class="screen-reader-text">按回车来打开此小节</span>
                            </h3>
                            <div class="accordion-section-content " style="padding: 10px">
                                <div class="inside" style="margin: 0" >
                                   <p style="margin: 0">{{setting.section_description}}</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div id="menu-management-liquid" >
                <div id="menu-management">
                        <h2>Options</h2>
                        <div class="menu-edit">
                            <div id="nav-menu-header" style="border-bottom: 1px solid #dcdcde;margin-bottom: 0;">
                                <div class="major-publishing-actions wp-clearfix" style="padding-top: 10px;padding-bottom: 10px; ">
                                    <label class="menu-name-label" for="menu-name">
                                    {{option_group}}
                                    </label>
                                </div>
                            </div>
                            <div  style="padding: 0 10px;border-top: 1px solid #fff;border-bottom: 1px solid #dcdcde;background: #fff;">
                                <div v-if="html==''" class="wp-clearfix" style="background-color: white">
                                    <div class="drag-instructions post-body-plain">
                                        <p>什么也没有</p>
                                    </div>
                                </div>
                                <div ></div>
                                <form @submit.prevent="fromSubmit" id="sections" action="/wp-admin/options.php" method="post" v-html="html">
                                </form>
                            </div>

                            <div style="height: auto;padding: 0 10px;">
                                <div class="major-publishing-actions wp-clearfix" style="padding: 10px 0;">
                                    <div  style="float: left">
                                        <input type="submit"  @click="fromSubmit"
                                               class="button button-primary button-large menu-save" value="提交">
                                    </div><!-- END .publishing-action -->
                                </div>
                            </div>
                        </div>

                </div>
            </div>
        </div>
    </div>
<?php
$js=<<<JS
new Vue({
    el: '.wrap',
    data(){
        return {
            title:"iMessage",
            modalTitle:'',
            modelName:"{PHP_CODE_modelName}",
            activeUrl:'{PHP_CODE_activeUrl}',
            mysqlTableName:"{PHP_CODE_mysqlTableName}",
            navTab:{PHP_CODE_submenu},
            settings:{PHP_CODE_settings},
            activeSetting:'',
            html:'',
           
        }
    },
    watch:{
        activeSetting(index){
            this.SectionChange(index)
        }
    },
    created() {
        // 备份初始数据
        document.querySelector('.wrap').style.display = 'block';
    },
    computed:{
        option_group(){
            return  "imessage_group_"+ this.activeSetting
        }
    },
    methods:{
        SectionChange(key){
            // let Section  =(this.activeSetting)[this.activeSetting];
            let option_group = "imessage_group_"+ key
         
           jQuery.ajax({
                url: ajaxurl+"?action="+this.activeUrl,
                type: 'GET',
                dataType: 'html',
                data: {option_group:option_group},
                success: (res) => {
                    this.html  = res
                },
                error: (res) => {
                  console.log(res);
                }
            }) 
        },
        fromSubmit(){
            document.querySelector('input[name="_wp_http_referer"]').value='/wp-admin/admin.php?page='+this.activeUrl;
            document.getElementById('sections').submit();
        },
        FirstLetter(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    },
    mounted(){
        
    }
});

JS;
$this->registerJs(php_code_to_js_code(get_defined_vars()));