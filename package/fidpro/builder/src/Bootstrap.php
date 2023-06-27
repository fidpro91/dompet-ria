<?php

namespace fidpro\builder;

use Illuminate\Support\Str;

/**
 * FORMULA GENERATOR
 * 
 */
class Bootstrap
{
    /**
     *
     * @param array $data
     * @return integer
     */
    private static $defaultContent = '<div class="text-center"><div class="spinner-border text-primary m-2 text-center" role="status"><span class="sr-only ">Loading...</span></div></div>';

    public static function table($name, $atrr, $header, $body = null, $footer = null)
    {
        $html = '<table id="' . $name . '" ' . self::array_to_attr($atrr) . '>';
        $thead = "<thead>\n<tr>";
        foreach ($header as $key => $val) {
            if (is_array($val)) {
                $thead .= '<th ' . self::array_to_attr($val) . '>' . Str::upper($key) . '</th>' . "\n";
            } else {
                $thead .= '<th>' . $val . '</th>' . "\n";
            }
        }
        $thead .= "</tr>\n</thead>";
        //set body
        $tbody = "<tbody>\n";
        if (is_array($body)) {
            foreach ($body as $key => $val) {
                if (is_array($val)) {
                    $tbody .= "<tr>\n";
                    foreach ($val as $rs) {
                        $tbody .= '<td>' . $val . '</td>' . "\n";
                    }
                    $tbody .= "</tr>\n";
                }
            }
        }
        $tbody .= "</tbody>";
        $html = $html . "\n" . $thead . "\n" . $tbody . "\n</table>";
        echo $html;
    }

    public static function tableData($data, $atrr,$column = null)
    {
        if (empty($data)) {
            return false;
        }
        $html = '<table ' . self::array_to_attr($atrr) . '>';
        if (is_object($data)) {
            $data = $data->toArray();
        }

        if ($column) {
            $thead = "<thead>\n<tr>";
            $header = array_keys($column);
            foreach ($header as $key => $value) {
                $thead .= '<th>' . $value . '</th>' . "\n";
            }
        }else{
            $thead = "<thead>\n<tr><th>NO</th>";
            foreach ($data[0] as $key => $val) {
                $thead .= '<th>' . Str::upper(Str::replace('_',' ',$key)) . '</th>' . "\n";
            }
        }
        $thead .= "</tr>\n</thead>";
        //set body
        $tbody = "<tbody>\n";
        foreach ($data as $key => $val) {
            $tbody .= "<tr>\n";
            if ($column) {
                if (is_object($val)) {
                    $val = $val->toArray();
                }
                foreach ($column as $row) {
                    if ($row['data'] == 'number') {
                        $isi = ($key+1);
                    }else{
                        $isi = $val[$row['data']];
                    }
                    if (isset($row['custom'])) {
                        $isi = call_user_func($row['custom'],$val);
                    }
                    $tbody .= '<td>' . $isi . '</td>' . "\n";
                }
            }else{
                if (is_array($val) || is_object($val)) {
                    $tbody .= '<td>' . ($key+1) . '</td>';
                    foreach ($val as $rs) {
                        $tbody .= '<td>' . $rs . '</td>' . "\n";
                    }
                }
            }
            $tbody .= "</tr>\n";
        }
        $tbody .= "</tbody>";
        $html = $html . "\n" . $thead . "\n" . $tbody . "\n</table>";
        return $html;
    }

    public static function DataTable($name, $atrr, $data)
    {
        $html = '<table id="' . $name . '" ' . self::array_to_attr($atrr) . '>';
        $thead = "<thead>\n<tr>";
        foreach ($data['raw'] as $key => $val) {
            if (is_array($val)) {
                $thead .= '<th>' . Str::upper(Str::replace('_',' ',$key)) . '</th>' . "\n";
            }else{
                $thead .= '<th>' . Str::upper(Str::replace('_',' ',$val)) . '</th>' . "\n";
            }
        }
        $thead .= "</tr>\n</thead>";
        //set body
        $tbody = "<tbody>\n";
        $tbody .= "</tbody>";
        $html = $html . "\n" . $thead . "\n" . $tbody . "\n</table>";

        //SET DATA TABLE
        $defaultSetting = [
            "processing" => "true", 
            "serverSide" => "true"
        ];
        if (isset($data['dataTable'])) {
            $defaultSetting = array_merge($defaultSetting,$data['dataTable']);
        }
        $column = "";
        foreach ($data['raw'] as $key => $val) {
            if (is_array($val)) {
                $column .= "{";
                foreach ($val as $r => $v) {
                    if ($r == "settings") {
                        $settingC="";
                        foreach ($v as $rr => $vv) {
                            $settingC .= $rr." : "."$vv,\n";
                        }
                        $column .= rtrim($settingC,",\n");
                    }else{
                        $column .= "'$r'"." : "."'$v',\n";
                    }
                }
                $column .= "}";
            }else{
                $column .=  '{
                    "data" : "'.$val.'",
                    "name" : "'.$val.'"
                }';
            }
            $column .=",\n";
        }
        $column = rtrim($column,",\n");

        $varSetting = "";
        foreach ($defaultSetting as $key => $setting) {
            if (is_array($setting)) {
                $settinger = "{\n";
                foreach ($setting as $set => $res) {
                    if (is_array($res)) {
                        $res = implode(',',$res);
                        $res = "[".$res."]";
                    }
                    $settinger .= $set." : ".$res.",\n";
                }
                $settinger = rtrim($settinger,",\n")."\n}";
            }else{
                $settinger = $setting.",\n";
            }
            $varSetting .= $key .":". $settinger;
        }
        $varSetting = rtrim($varSetting,",\n");

        $filter = "";
        if (isset($data["filter"])) {
            foreach ($data["filter"] as $key => $value) {
                $filter .= "d.".$key." = ".$value.";\n";
            }
        }
        $variableTable = "tb_".str_replace('-','_',$name);
        $script = '
        <script>
        var '.$variableTable.';
        $(document).ready(function() {
            '.$variableTable.'  = $("#'.$name.'").DataTable({
                "ajax" : {
                        "url":"'.url($data["url"]).'",
                        "data" : function(d){
                            '.$filter.'
                        }
                },
                "columns" :[
                    '.$column.'
                ],
                '.$varSetting.'
            });
        })
        </script>
        ';
        $html .= "\n".$script;
        echo $html;
    }

    public static function tabs($data)
    {
        $html = '<ul class="nav nav-tabs mb-3">';
        $i = 0;
        if (is_callable($data['tabs'])) {
            $data['tabs'] = call_user_func($data['tabs']);
        }
        $contents = '<div class="tab-content" id="'.($data['id']??'tab').'">';
        foreach ($data['tabs'] as $key => $value) {
            $click = $active = '';
            if ($i == 0) {
                $active = 'active';
            }
            if (isset($value['url'])) {
                $click = 'onclick="loadTab'.($data['id']??'').'(\''.$value['href'].'\',\''.url($value['url']).'\')"';
            }
            $html .= '
            <li class="nav-item">
                <a href="#' . $value['href'] . '" data-toggle="tab" aria-expanded="false" class="nav-link ' . $active . '" ' . $click . '>
                    <i class="mdi mdi-home-variant d-lg-none d-block mr-1"></i>
                    <span class="d-none d-lg-block">' . $key . '</span>
                </a>
            </li>';
            $clear = 'clear';
            $cont='';
            if (!empty($value['content'])) {
                if (is_callable($value['content'])) {
                    $value['content'] = call_user_func($value['content']);
                }
                $clear = '';
                $cont = $value['content'];
            }
            $contents .= '<div class="tab-pane '.$clear.' ' . $active . '" id="' . $value['href'] . '">';
            $contents .= $cont;
            $contents .= "</div>";
            $i++;
        }
        $contents .= "</div>";
        $html .= '</ul>';

        $script = "
        <script>
            function loadTab".($data['id']??'')."(a,b){
                $('#".($data['id']??'tab')."').find('.tab-pane.clear').html('".self::$defaultContent."');
                $('#'+a+'').load(b);
            }
        </script>";

        echo $html . "\n" . $contents."\n".$script;
    }

    private function array_to_attr($attr)
    {
        $ret = '';
        foreach ($attr as $key => $value) {
            $ret .= ' ' . htmlspecialchars($key, ENT_QUOTES) . '="' . htmlspecialchars($value) . '"';
        }
        return trim($ret);
    }

    public function modal($id, $data)
    {

        $attr = null;
        if(isset($data['attr'])){
            $attr = self::array_to_attr($data['attr']);
        }
        $txt = '<div class="modal fade" id="' . $id . '">
	        <div class="modal-dialog ' . $data['size'] . '" ' .$attr. '>
	          <div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="' . $id . '">' . $data['title'] . '</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
	            <div class="modal-body" id="modal-body">';
        $content = self::$defaultContent;
        if (isset($data['body']['content'])) {
            if (is_callable($data['body']['content'])) {
                $data['body']['content'] = call_user_func($data['body']['content']);
            }
            $content = $data['body']['content'];
        }
        $txt .= $content;
        $txt .= self::modal_close($id, $data);
        echo $txt;
    }

    public function modal_close($id, $data)
    {
        $autohide = true;
        if (!empty($data['body']['content'])) {
            $autohide = false;
        }
        $txt = '</div>';
        if (isset($data['footer']['content'])) {
            $txt .= '
            <div class="modal-footer">';
            $footer = $data['footer']['content'];
            foreach ($footer as $key => $value) {
                $txt .= $value . "\n";
            }
            $txt .= '</div>';
        }
        $txt .= '
	          </div>
	        </div>
	        </div>';
        $txt .= '<script>';
        if (isset($data['body']['url'])) {
            $txt .= '
		$("#' . $id . '").on(\'shown.bs.modal\',function(){
			$(this).find(\'.modal-body\').load("' . URL($data['body']['url']) . '");
		  });
		';
        }
        if ($autohide != false) {
            $txt .= '
                $("#' . $id . '").on(\'hidden.bs.modal\',function(){
                    $(this).find(\'.modal-body\').html(\''.self::$defaultContent.'\');
                });
                ';
        }
        $txt .= '</script>';
        return $txt;
    }
}
