<?php
/** @var $this yii\web\View */
/** @var $model crud\modules\pay\models\Order */
/** @var $url string */
/** @var $file array */
/** @var $code  */
/** @var $submenu string */
use \imessage\assets\VueAsset;
VueAsset::register($this);
?>
<div class="wrap">
    <div class="wrap">
        <h1 class="wp-heading-inline">{{title}}</h1>
        <hr class="wp-header-end">
        <ul class="nav-tab-wrapper wp-clearfix">
            <a v-for="(url,index) in navTab"
               :href="'/wp-admin/admin.php?page=' +url.url"
               :class="(page == url.url)?'nav-tab nav-tab-active':'nav-tab'">{{url.text}}</a>
        </ul>
    </div>
    <div class="tablenav top" style="margin-bottom: 0px">
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
            <td  class="manage-column"><span>正则</span></td>
            <td  class="manage-column"><span>重写规则</span></td>
        </tr>
        </thead>
        <tbody id="the-list">
        <template v-for="(value,key,itemIndex) in rules ">
            <tr  v-if="itemIndex>=startNumber && itemIndex<=endNumber">
                <th scope="row" class="check-column">
                    <label class="screen-reader-text" for="cb-select-1">选择</label>
                    <input id="cb-select-1" type="checkbox" name="post[]" >
                    <div class="locked-indicator">
                        <span class="locked-indicator-icon" aria-hidden="true"></span>
                    </div>
                </th>
                <td class="manage-column" data-colname="名称">{{itemIndex+1}}</td>
                <td class="manage-column" data-colname="名称">{{key}}</td>
                <td class="manage-column" data-colname="名称">{{value}}</td>
            </tr>
        </template>

        </tbody>
        <tfoot>
        <tr>
            <td id="cb" class="manage-column column-cb check-column">
                <label class="screen-reader-text" for="cb-select-all-1">全选</label>
                <input id="cb-select-all-1" type="checkbox">
            </td>
            <td  class="manage-column"><span>正则</span></td>
            <td  class="manage-column"><span>正则</span></td>
            <td  class="manage-column"><span>重写规则</span></td>
        </tr>
        </tfoot>
    </table>
</div>
<?php
//$json = json_encode($rules);
$js=<<<JS
new Vue({
    el: '.wrap',
    data(){
        return {
            title:"iMessage",
            rules:{},
            tableSize:10,
            index:1,
            navTab:{PHP_CODE_submenu},
        }
    },
    watch:{
       index(value){
           console.log([this.pageLength,this.index,this.startNumber,this.endNumber])
       } 
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
        }
       
    },
    methods:{
        init(){
            jQuery.ajax({
                url: ajaxurl,
                type: 'GET',
                data: {
                  action: 'index/rules'
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
           
        }
    },
    mounted(){
        this.init();
    }
});
JS;
$this->registerJs(php_code_to_js_code(get_defined_vars()));