<?php
 
namespace fidpro\builder;

use Illuminate\Support\Str;

/**
 * FORMULA GENERATOR
 * 
 */
class Create
{
    /**
     *
     * @param array $data
     * @return integer
     */
    public static $form = '';
    public static $formId = '';
    public static $formLabel = '';
    protected static $className = "form-control";

    public static function input($name,$attr=null)
    {
        $defaultAttr = [
            "class"         => self::$className,
            "type"          => "text",
            "name"          => "$name", 
            "placeholder"   => Str::ucfirst(Str::replace('_', ' ', $name))
        ];
        if(is_array($attr)){
            $defaultAttr = array_merge($defaultAttr,$attr);
        }
        $properti = self::array_to_attr($defaultAttr);
        $form = "<input id=\"$name\" $properti></input>";
        return self::_set_output($form,$name);
    }

    public static function upload($name,$attr=null)
    {
        $defaultAttr = [
            "class"         => self::$className."-file",
            "type"          => "file",
            "placeholder"   => Str::ucfirst(Str::replace('_', ' ', $name))
        ];
        if(is_array($attr)){
            $defaultAttr = array_merge($defaultAttr,$attr);
        }
        $properti = self::array_to_attr($defaultAttr);
        $form = "<input id=\"$name\" name = \"$name\" $properti></input>";
        return self::_set_output($form,$name);
    }

    private function _set_output ($form,$name){
        static::$form = $form;
        static::$formId = $name;
        static::$formLabel = Str::ucfirst(Str::replace('_', ' ', $name));
        return new static;
    }

    public static function render($type = null,$label = null) {
        $render =  static::$form;
        if($type == 'group') {
            $render = '<div class="form-group">
                <label for="' . static::$formId . '">' .($label??static::$formLabel). '</label>';
            $render .= static::$form."</div>";

        }
        return $render;
    }

    public static function text($name,$attr=null)
    {
        $defaultAttr = [
            "class"     => "form-control",
            "id"        => $name,
            "name"      => $name,
        ] ;
        if (isset($attr['option'])) {
            $defaultAttr=array_merge($defaultAttr,$attr['option']);
        }
        $form = "<textarea ".self::array_to_attr($defaultAttr).">".($attr['value']??"")."</textarea>";
        return self::_set_output($form,$name);
    }

    public static function dropDown($id, $attr)
    {
        if (isset($attr['data']['model'])) {
            $data = $attr['data'];
            $model = "\\App\\Models\\" . $data['model'];
            $filter = $data['filter'] ?? [];
            if (isset($data['custom'])) {
                $model = new $model;
                $dataSelect = $model->{$data['custom']}($filter);
            } else {
                if (isset($data['filter'])) {

                    $dataSelect = $model::where($data['filter'])
                    ->get();
                } else {
                    $dataSelect = $model::all();
                }
            }
            $dataDropdown = [];
            foreach ($dataSelect as $key => $value) {
                if (isset($data['column'])) {
                    if (count($data["column"])>1) {
                        $val = $value->{$data['column'][0]};
                        $text = $value->{$data['column'][1]};
                    }else{
                        $val = $text = $value->{$data['column'][0]};
                    }
                } else {
                    $val = (is_numeric($key)?$value:$key);
                    $text = $value;
                }
                $selected = "";
                if (!empty($attr["selected"]) && ($val == $attr["selected"])) {
                    $selected = "selected";
                }
                $dataDropdown[] = "<option $selected value=\"$val\">" . $text . "</option>";
            }
        }else{
            $data = $attr['data'];
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $val = key($value);
                    $text = current($value);
                } else {
                    $val = $text = $value;
                }
                $selected = "";
                
                if (!empty($attr["selected"]) && ($val == $attr["selected"])) {
                    $selected = "selected";
                }
                
                $dataDropdown[] = "<option $selected value=\"$val\">" . $text . "</option>";
            }
        }
        $defaultAttr = [
            "class"     => self::$className,
            "id"        => $id,
            "name"      => $id,
        ];
        if (isset($attr['extra'])) {
            $defaultAttr = array_merge($defaultAttr, $attr['extra']);
        }
        $select = "<select " . self::array_to_attr($defaultAttr) . "><option value=\"\">---</option>" . implode("\n", $dataDropdown) . "</select>";
        return self::_set_output($select,$id);
    }

    public static function radio($id, $attr)
    {
        if (isset($attr['data']['model'])) {
            $data = $attr['data'];
            $model = "\\App\\Models\\" . $data['model'];
            $filter = $data['filter'] ?? [];
            if (isset($data['custom'])) {
                $model = new $model;
                $dataSelect = $model->{$data['custom']}($filter);
            } else {
                if (isset($data['filter'])) {

                    $dataSelect = $model::where($data['filter'])
                    ->get();
                } else {
                    $dataSelect = $model::all();
                }
            }
            $dataDropdown = [];
            foreach ($dataSelect as $key => $value) {
                if (isset($data['column'])) {
                    $dataDropdown[] = '<div class="custom-control custom-radio">
                        <input type="radio" id="'.$id.$key.'" name="'.$id.'" class="custom-control-input" value="'. $value->{$data['column'][0]} .'">
                        <label class="custom-control-label" for="'.$id.$key.'">'.$value->{$data['column'][1]}.'</label>
                    </div>';
                } else {
                    $dataDropdown[] = '<div class="custom-control custom-radio">
                        <input type="radio" id="'.$id.$key.'" name="'.$id.'" class="custom-control-input" value="'. $value.'">
                        <label class="custom-control-label" for="'.$id.$key.'">'.$value.'</label>
                    </div>';
                }
            }
        }else{
            $data = $attr['data'];
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $dataDropdown[] = '<div class="custom-control custom-radio">
                        <input type="radio" id="'.$id.$key.'" name="'.$id.'" class="custom-control-input" value="'. key($value) .'">
                        <label class="custom-control-label" for="'.$id.$key.'">'.current($value).'</label>
                    </div>';
                } else {
                    $dataDropdown[] = '<div class="custom-control custom-radio">
                        <input type="radio" id="'.$id.$key.'" name="'.$id.'" class="custom-control-input" value="'. $value.'">
                        <label class="custom-control-label" for="'.$id.$key.'">'.$value.'</label>
                    </div>';
                }
            }
        }
        $radio = implode("\n", $dataDropdown);
        return self::_set_output($radio,$id);
    }
    
    public static function checkbox($id, $attr)
    {
        if (isset($attr['data']['model'])) {
            $data = $attr['data'];
            $model = "\\App\\Models\\" . $data['model'];
            $filter = $data['filter'] ?? [];
            if (isset($data['custom'])) {
                $model = new $model;
                $dataSelect = $model->{$data['custom']}($filter);
            } else {
                if (isset($data['filter'])) {
                    $dataSelect = $model::where($data['filter'])
                    ->get();
                } else {
                    $dataSelect = $model::all();
                }
            }
            $dataDropdown = [];
            foreach ($dataSelect as $key => $value) {
                if (isset($data['column'])) {
                    $dataDropdown[] = '<div class="custom-control custom-checkbox">
                        <input type="checkbox" id="'.$id.$key.'" name="'.$id."[".$key."]".'" class="custom-control-input" value="'. $value->{$data['column'][0]} .'">
                        <label class="custom-control-label" for="'.$id.$key.'">'.$value->{$data['column'][1]}.'</label>
                    </div>';
                } else {
                    $dataDropdown[] = '<div class="custom-control custom-checkbox">
                        <input type="checkbox" id="'.$id.$key.'" name="'.$id."[".$key."]".'" class="custom-control-input" value="'. $value.'">
                        <label class="custom-control-label" for="'.$id.$key.'">'.$value.'</label>
                    </div>';
                }
            }
        }else{
            $data = $attr['data'];
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $dataDropdown[] = '<div class="custom-control custom-checkbox">
                        <input type="checkbox" id="'.$id.$key.'" name="'.$id."[".$key."]".'" class="custom-control-input" value="'. key($value).'">
                        <label class="custom-control-label" for="'.$id.$key.'">'.current($value).'</label>
                    </div>';
                } else {
                    $dataDropdown[] = '<div class="custom-control custom-checkbox">
                        <input type="checkbox" id="'.$id.$key.'" name="'.$id."[".$key."]".'" class="custom-control-input" value="'. $value.'">
                        <label class="custom-control-label" for="'.$id.$key.'">'.$value.'</label>
                    </div>';
                }
            }
        }
        $checkbox = implode("\n", $dataDropdown);
        return self::_set_output($checkbox,$id);
    }

    private function array_to_attr($attr) {
        $ret = '';
        foreach ($attr as $key => $value) {
            $ret .= ' ' . htmlspecialchars($key, ENT_QUOTES) . '="' . htmlspecialchars($value) . '"';
        }
        return trim($ret);
    }

    public static function action($title,$attr) {
        $html = '<a href="javascript:void(0)" '.self::array_to_attr($attr).'>'.$title.'</a>';

        return $html;
    }

    public static function link($title,$attr) {
        $html = '<a '.self::array_to_attr($attr).'>'.$title.'</a>';

        return $html;
    }
}