<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" language="JavaScript">

<?php
//put the content of allfields.txt into two-dimentional array
//COL1: fileds prefix, 
//COL2: last index of the checkbox in the group
//COL3: index of the textbox in the group
//COL4: if the NL in the group is default NL 
//COL5: indicates the fields for male only or female only, 0 if unisex
//NOTE: textbox in the group must have the last indexes
$myFile = "allfields.txt";
$fh = fopen($myFile, 'r');
global $fields;
$fields = array();
while (!feof($fh))
{
    $theData = trim(fgets($fh));
    $line_items = explode(",",$theData);
    array_push($fields, $line_items);
}
fclose($fh);

//put value in $fields into javascript array
$num_row = sizeof($fields);
echo "var fields = new Array(".$num_row.");\n";
for ( $i = 0 ; $i < $num_row; $i++)
{
    echo "fields[".$i."] = new Array(5);\n";
    //for ( $j = 0; $j < sizeof($fields[$i]); $j++)
    for ( $j = 0; $j < 5; $j++)
    {
        echo "fields[".$i."][".$j."] = \"".$fields[$i][$j]."\";\n";
    }
}
?>

//get pt gender,default is male
gender = 'pt_male';

function getGender(value)
{
    gender =  value;
}

function CheckAllNL(x)
{     
    //if x == 1, check all default NL
    if (x)
    {
        for (var i = 0; i < fields.length; i++)
        {
            if ( fields[i][3] == 1 ) //if checkbox is one of the default NL
            {
                if (gender == 'pt_male')//male pt NL defaults only
                {
                    if (fields[i][4] == 'f') //if current item is female item, make it false
                    {     
                            eval('document.physical_form.' + fields[i][0] + '_0.checked = false');    
                    }
                    else 
                    {
                            eval('document.physical_form.' + fields[i][0] + '_0.checked = true');    
                    }
                }
                else  if (gender == 'pt_female')//female pt NL defaults only
                {
                    if (fields[i][4] == 'm') //if current item is male item, make it false
                    {     
                            eval('document.physical_form.' + fields[i][0] + '_0.checked = false');    
                    }
                    else 
                    {
                            eval('document.physical_form.' + fields[i][0] + '_0.checked = true');    
                    }
                }
            }// end - if (gender == 'pt_male')
        }//end of for
    }//end of if(x)
            
    else //uncheck all defaults
    {
        for (var i = 0; i < fields.length; i++)
        {
            eval('document.physical_form.' + fields[i][0] + '_0.checked = false');    
        }
    }
        
}

function CheckboxRule(check_name, status)    
{    
    if ((status == true) || (status != ""))
    {
        var name_items = check_name.split("_");
        //user cookie to pass current checkbox name to php script        
        //if current checkbox is NL
        //uncheck all other checkboxes in the same group
        //and clear the textbox
        //name_items[0] is the prefix
        
        if ( (parseInt(name_items[1])) == 0 )
        {
            var last_index = 0;
            var textbox_index;
            //get last item index and texbox index 
            //from fields        
            for (var i = 0; i < fields.length; i++)
            {
                if (fields[i][0] == name_items[0])
                {
                    last_index = fields[i][1];
                    textbox_index = fields[i][2];
                    break;
                }
            }
            
            //if there is/are textbox(es) in the group, clear its value
            if ( (parseInt(textbox_index)) != 0)
            {

                    //if one group has more than one textbox, min(array length) = 3
                    if (textbox_index.length > 2 )
                    {
                        //save all textbox (in the same group) index in an array
                        var textbox_arr = textbox_index.split(" ");
                        for (var k=0; k < textbox_arr.length; k++)
                        {
                            eval('document.physical_form.' + name_items[0] + '_' + textbox_arr[k] + '_t.value = ""');            
                        }
                    }
                        
                    else
                    {
                        eval('document.physical_form.' + name_items[0] + '_' + textbox_index + '_t.value = ""');    
                    }
                    
            }// end of if there is textbox
                    
            for( var j = 1; j <= last_index; j++ )
            {
                    eval('var radio_len = document.physical_form.' + name_items[0] + '_' + j + '.length;');
                    //if current item is radio buttion
                    //loop though all radio buttons in one group and clear them
                    if (radio_len > 1)
                    {
                          for (var n = 0; n < radio_len; n++)
                        {
                             eval('document.physical_form.' + name_items[0] + '_' + j + '[' + n + '].checked = false');
                        }
                    }
                    
                    else //regular checkbox
                    {
                        eval('document.physical_form.' + name_items[0] + '_' + j + '.checked = false');
                    }
                
            }// for of "for loop" -- put all abnormal options to false        
                    
        }    //end of "if checkbox is NL"    
        
        //if any of the abnormal options in the group is checked, 
        //clear the corresponding NL checkbox
        else
        {
            eval('document.physical_form.' + name_items[0] + '_0.checked = false');
        }
     }//end of "if status is true"
 }//end of function

</script>
<title>Urology Physical Exam</title>
<style type="text/css">
<!--
.TBcontent {font-size: small}
-->
</style>
</head>
<h3>Urology Physical Exam</h3>
<body>
<form action="report.php" method="post" name="physical_form">
  <table border="0" id="pt_info">
    <tr>
      <td></td>
      <td><input name="pt_type" type="radio" value="new_pt">
        New patient</td>
      <td></td>
      <td><input name="pt_type" type="radio" value="est_pt">
        Established patient</td>
    </tr>
      <td></td>
      <td><input name="pt_gender" type="radio" value="pt_male"  checked="checked" onClick="getGender(value)" >
        Male</td>
      <td></td>
      <td><input name="pt_gender" type="radio" value="pt_female"  onClick="getGender(value)">
        Female</td>
    </tr>
    <tr>
      <td align="right">Date </td>
      <td><input name="date" type="text"></td>
      <td align="right">Requested by </td>
      <td><input name="request" type="text"></td>
    </tr>
    <tr>
      <td align="right"> Patient Name </td>
      <td><input name="name" type="text"></td>
      <td align="right">      MRN </td>
      <td><input name="mrn" type="text"></td>
    </tr>
    <tr>
      <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
      <td><input type="button" value="Check All Normal" name="btn_checkall" onClick="CheckAllNL(1)" ></td>
      <td><input type="button" value="Uncheck All Normal" name="btn_uncheckall"onClick="CheckAllNL(0)"></td>
    </tr>
  </table>
  <table border="0" id="general_info">
    <tr>
      <td><strong>PX</strong></td>
      <td></td>
    </tr>
    <tr>
      <td>at least 3 VS</td>
      <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
      <td class="TBcontent" align="right">Height</td>
      <td class="TBcontent"><input name="ht_foot" type="text" size="2">
        ft
        <input name="ht_inch2" type="text" size="2">
        inch</td>
      <td align="right" class="TBcontent">Weight</td>
      <td><input name="ht_inch" type="text" size="2">
        <font class="TBcontent">lbs</font></td>
    </tr>
    <tr>
      <td align="right" class="TBcontent">&nbsp;</td>
      <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
      <td align="right" class="TBcontent"> BP</td>
      <td><input name="bp" type="text" size="8">
        <font class="TBcontent">mmHg</font></td>
      <td align="right" class="TBcontent"> Pulse</td>
      <td><input name="pulse" type="text" size="5"></td>
    </tr>
    <tr> </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
      <td height="28" align="right" class="TBcontent">Resp</td>
      <td><input name="resp" type="text" size="5"></td>
      <td align="right" class="TBcontent"> Temp</td>
      <td><input name="temp" type="text" size="5"></td>
    </tr>
  </table>
  <table border="1" id="physical_exam" >
    <tr>
      <td><strong>GENERAL</strong></td>
      <td>APPEARANCE</td>
      <td class="TBcontent"><input name="appearance_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)">
        NL&nbsp;&nbsp;
        <input name="appearance_1" type="checkbox" id="appearance_1" value="cachectic" onClick="CheckboxRule(name,checked)">
        Cachectic&nbsp;&nbsp;
        <input name="appearance_2" type="checkbox" id="appearance_2" value="deformities" onClick="CheckboxRule(name,checked)">
        Deformities </td>
    </tr>
    <tr>
      <td><strong>NEURO</strong></td>
      <td>ORIENTATION</td>
      <td class="TBcontent"><input name="orientation_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)"  >
        NL&nbsp;&nbsp;
        <input name="orientation_1" type="checkbox" value="time" onClick="CheckboxRule(name,checked)">
        Time&nbsp;&nbsp;
        <input name="orientation_2" type="checkbox" value="place" onClick="CheckboxRule(name,checked)" >
        Place&nbsp;&nbsp;
        <input name="orientation_3" type="checkbox" value="person" onClick="CheckboxRule(name,checked)">
        Person&nbsp;&nbsp;
        
        Other
        <input name="orientation_4_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="orientation_4_t_type" type="hidden" value="text">      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>MOOD/AFFECT</td>
      <td class="TBcontent">
        <input name="mood_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL&nbsp;&nbsp;
        <input name="mood_1" type="checkbox" value="depression" onClick="CheckboxRule(name,checked)" >
        Depression&nbsp;&nbsp;
        <input name="mood_2" type="checkbox" value="anxiety" onClick="CheckboxRule(name,checked)" >
        Anxiety
        &nbsp;&nbsp;
        <input name="mood_3" type="checkbox" value="agitation" onClick="CheckboxRule(name,checked)" >
        Agitation </td>
    </tr>
   
    <tr>
      <td><strong>SKIN</strong></td>
      <td>&nbsp;</td>
      <td class="TBcontent"><input name="skin_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL&nbsp;&nbsp;
        <input name="skin_1" type="checkbox" value="jaundice" onClick="CheckboxRule(name,checked)" >
        Jaundice&nbsp;&nbsp;
        <input name="skin_2" type="checkbox" value="pale" onClick="CheckboxRule(name,checked)" >
        Pale&nbsp;&nbsp;
        <input name="skin_3" type="checkbox" value="cyanosis" onClick="CheckboxRule(name,checked)" >
        Cyanosis&nbsp;&nbsp;
        <input name="skin_4" type="checkbox" value="lesions" onClick="CheckboxRule(name,checked)" >
        Lesions&nbsp;&nbsp;
        <input name="skin_5" type="checkbox" value="rash" onClick="CheckboxRule(name,checked)" >
        Rash&nbsp;&nbsp;
        
        Other
        <input name="skin_6_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="skin_6_t_type" type="hidden" value="text"></td>
    </tr>
    <tr>
      <td><strong>NECK</strong></td>
      <td>NECK</td>
      <td class="TBcontent"><input name="neck_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL&nbsp;&nbsp;
         <input name="neck_1" type="checkbox" value="symmetry" onClick="CheckboxRule(name,checked)" >
        Symmetry&nbsp;&nbsp;
        <input name="neck_2" type="checkbox" value="swelling" onClick="CheckboxRule(name,checked)" >
        Swelling&nbsp;&nbsp;
        <input name="neck_3" type="checkbox" value="tenderness" onClick="CheckboxRule(name,checked)" >
        Tenderness&nbsp;&nbsp;
        Other
        <input name="neck_4_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="neck_4_t_type" type="hidden" value="text">      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>THYROID</td>
      <td class="TBcontent"><input name="thyroid_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL&nbsp;&nbsp;
        
        Size
        <input name="thyroid_3_t" type="textbox" value="" oninput="CheckboxRule(name,value)" size="2" >
        <input name="thyroid_3_t_type" type="hidden" value="numeric">
        &nbsp;&nbsp;
        <input name="thyroid_1" type="checkbox" value="tenderness" onClick="CheckboxRule(name,checked)" >
        Tenderness&nbsp;&nbsp;
        <input name="thyroid_2" type="checkbox" value="nodules" onClick="CheckboxRule(name,checked)" >
        Nodules&nbsp;&nbsp;
        
        Other
        <input name="thyroid_4_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="thyroid_4_t_type" type="hidden" value="text"></td>
    </tr>
    <tr>
      <td><strong>RESP</strong></td>
      <td>EFFORT</td>
      <td class="TBcontent"><input name="effort_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL
        <input name="effort_1" type="checkbox" value="labored" onClick="CheckboxRule(name,checked)" >
        Labored
        <input name="effort_2" type="checkbox" value="diaphragmatic" onClick="CheckboxRule(name,checked)" >
        Diaphragmatic
        <input name="effort_3" type="checkbox" value="abdominal" onClick="CheckboxRule(name,checked)" >
        Abdominal </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>AUSCULTATION</td>
      <td class="TBcontent"><input name="rsepauscultation_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL
        <input name="rsepauscultation_1" type="checkbox" value="rales" onClick="CheckboxRule(name,checked)" >
        Rales
        <input name="rsepauscultation_2" type="checkbox" value="rhonchi" onClick="CheckboxRule(name,checked)" >
        Rhonchi
        <input name="rsepauscultation_3" type="checkbox" value="wheezes" onClick="CheckboxRule(name,checked)" >
        Wheezes  
        
        Other
        <input name="rsepauscultation_4_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
        <input name="rsepauscultation_4_t_type" type="hidden" value="text"></td>
    </tr>
    <tr>
      <td><strong>CV</strong></td>
      <td>AUSCULTATION</td>
      <td class="TBcontent"><input name="cvauscultation_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL&nbsp;&nbsp;
        <input name="cvauscultation_1" type="checkbox" value="rhythm" onClick="CheckboxRule(name,checked)" >
        Rhythm&nbsp;&nbsp;
        <input name="cvauscultation_2" type="checkbox" value="murmurs" onClick="CheckboxRule(name,checked)" >
        Murmurs&nbsp;&nbsp;
        <input name="cvauscultation_3" type="checkbox" value="rubs" onClick="CheckboxRule(name,checked)" >
        Rubs&nbsp;&nbsp;
        
        Other
        <input name="cvauscultation_4_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="cvauscultation_4_t_type" type="hidden" value="text"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>PERIPHERAL</td>
      <td class="TBcontent"><input name="peri_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL&nbsp;&nbsp;
        <input name="peri_1" type="checkbox" value="swelling" onClick="CheckboxRule(name,checked)" >
        Swelling&nbsp;&nbsp;
        <input name="peri_2" type="checkbox" value="tenderness" onClick="CheckboxRule(name,checked)" >
        Tenderness&nbsp;&nbsp;
        <input name="peri_3" type="checkbox" value="varicosities" onClick="CheckboxRule(name,checked)" >
        Varicosities&nbsp;&nbsp;
        
        Other
        <input name="peri_4_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="peri_4_t_type" type="hidden" value="text"></td>
    </tr>
    <tr>
      <td><strong>LYMPHATIC</strong></td>
      <td>&nbsp;</td>
      <td class="TBcontent"> Neck:
        <input name="lymneck_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL&nbsp;&nbsp;
        <input name="lymneck_1" type="checkbox" value="enlarged" onClick="CheckboxRule(name,checked)" >
        Enlarged
        &nbsp;&nbsp;
        Axilla:
        <input name="lymaxilla_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL&nbsp;&nbsp;
        <input name="lymaxilla_1" type="checkbox" value="enlarged" onClick="CheckboxRule(name,checked)" >
        Enlarged&nbsp;&nbsp;
        Groin:
        <input name="lymgroin_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL&nbsp;&nbsp;
        <input name="lymgroin_1" type="checkbox" value="enlarged" onClick="CheckboxRule(name,checked)" >
        Enlarged&nbsp;&nbsp;
        
        Other
        <!-- for the loop in javascript not be interrupted, added lym_0 as an empty input -->
        <input name="lym_0" type="hidden" value=""> 
        <input name="lym_1_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="lym_1_t_type" type="hidden" value="text">      </td>
    </tr>
    <tr>
      <td><strong>GI</strong></td>
      <td>ABDOMEN</td>
      <td class="TBcontent">
        <input name="giabdomen_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL&nbsp;&nbsp;
        <input name="giabdomen_1" type="checkbox" value="mass" onClick="CheckboxRule(name,checked)" >
        Mass
        &nbsp;&nbsp;Location:
        <input name="giabdomen_2" type="checkbox" value="midline" onClick="CheckboxRule(name,checked)">
        Midline
        <input name="giabdomen_3" type="checkbox" value="RUQ" onClick="CheckboxRule(name,checked)">
        RUQ
        <input name="giabdomen_4" type="checkbox" value="RLQ" onClick="CheckboxRule(name,checked)">
        RLQ
        <input name="giabdomen_5" type="checkbox" value="LUQ" onClick="CheckboxRule(name,checked)">
        LUQ
        <input name="giabdomen_6" type="checkbox" value="LLQ" onClick="CheckboxRule(name,checked)">
        LLQ
        &nbsp;&nbsp;
        Size
        <input name="giabdomen_15_t" type="textbox" value="" oninput="CheckboxRule(name,value)" size="2">
        <input name="giabdomen_15_t_type" type="hidden" value="numeric">      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td class="TBcontent"><input name="giabdomen_7" type="checkbox" value="" onClick="CheckboxRule(name,checked)" >
        Tenderness:
        <input name="giabdomen_8" type="checkbox" value="midline" onClick="CheckboxRule(name,checked)">
        Midline
        <input name="giabdomen_9" type="checkbox" value="RUQ" onClick="CheckboxRule(name,checked)">
        RUQ
        <input name="giabdomen_10" type="checkbox" value="RLQ" onClick="CheckboxRule(name,checked)">
        RLQ
        <input name="giabdomen_11" type="checkbox" value="LUQ" onClick="CheckboxRule(name,checked)">
        LUQ
        <input name="giabdomen_12" type="checkbox" value="LLQ" onClick="CheckboxRule(name,checked)">
        LLQ
        <input name="giabdomen_13" type="checkbox" value="RCVA" onClick="CheckboxRule(name,checked)">
        RCVA
        <input name="giabdomen_14" type="checkbox" value="LCVA" onClick="CheckboxRule(name,checked)">
        LCVA &nbsp;
         Other
        <input name="giabdomen_16" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="giabdomen_16_t_type" type="hidden" value="text"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>HERNIA</td>
      <td class="TBcontent"><input name="hernia_0" type="checkbox" value="none" onClick="CheckboxRule(name,checked)" >
        NONE&nbsp;&nbsp;
        <input name="hernia_1" type="checkbox" value="R Groin" onClick="CheckboxRule(name,checked)">
        R Groin
        <input name="hernia_2" type="checkbox" value="L Groin" onClick="CheckboxRule(name,checked)">
        L Groin
        <input name="hernia_3" type="checkbox" value="umbilical" onClick="CheckboxRule(name,checked)">
        Umbilical
        <input name="hernia_4" type="checkbox" value="incisional" onClick="CheckboxRule(name,checked)">
        Incisional&nbsp;&nbsp;
        Other
        
              <input name="hernia_5_t_type" type="hidden" value="text">      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>LIVER</td>
      <td class="TBcontent"><input name="liver_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL&nbsp;&nbsp;
        <input name="liver_1" type="checkbox" value="palpable" onClick="CheckboxRule(name,checked)" >
        Palpable&nbsp;&nbsp;
        Other
        <input name="liver_2_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="liver_2_t_type" type="hidden" value="text">      
        </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>SPLEEN</td>
      <td class="TBcontent"><input name="spleen_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL&nbsp;&nbsp;
        <input name="spleen_1" type="checkbox" value="palpable" onClick="CheckboxRule(name,checked)" >
        Palpable&nbsp;&nbsp;
        Other
        <input name="spleen_2_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="spleen_2_t_type" type="hidden" value="text">      
      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>STOOL SPECIMEN</td>
      <td class="TBcontent"><input name="stool_0" type="checkbox" value="not indicated" onClick="CheckboxRule(name,checked)" >
        Not indicated &nbsp;&nbsp;
        <input name="stool_1" type="checkbox" value="indicated" onClick="CheckboxRule(name,checked)" >
        Indicated &nbsp;&nbsp;
        <input name="stool_2" type="radio" value="blood" onClick="CheckboxRule(name,checked)">
        Blood
        <input name="stool_2" type="radio" value="no Blood" onClick="CheckboxRule(name,checked)">
        No Blood </td>
    </tr>
    <tr>
      <td><strong>MALE GU</strong></td>
      <td>ANUS/PERINEUM</td>
      <td class="TBcontent"><input name="manus_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL &nbsp;&nbsp;
        <input name="manus_1" type="checkbox" value="fissures" onClick="CheckboxRule(name,checked)" >
        Fissures
        &nbsp;&nbsp;
              <input name="manus_2" type="checkbox" value="edema" onClick="CheckboxRule(name,checked)" size="2">
        Edema
        &nbsp;&nbsp;
              <input name="manus_3" type="checkbox" value="tenderness" onClick="CheckboxRule(name,checked)" >
        Tenderness
        &nbsp;&nbsp;
        Other
        <input name="manus_4_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="manus_4_t_type" type="hidden" value="text">      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>SCROTUM</td>
      <td class="TBcontent"><input name="mscrotum_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL
        &nbsp;&nbsp;
              <input name="mscrotum_1" type="checkbox" value="lesions" onClick="CheckboxRule(name,checked)" >
        Lesions
        &nbsp;&nbsp;
              <input name="mscrotum_2" type="checkbox" value="rash" onClick="CheckboxRule(name,checked)" >
        Rash
        &nbsp;&nbsp;
              <input name="mscrotum_3" type="checkbox" value="sebaceous cyst" onClick="CheckboxRule(name,checked)" >
        Sebaceous Cyst
        &nbsp;&nbsp;
              <input name="mscrotum_4" type="checkbox" value="hydorcele" onClick="CheckboxRule(name,checked)" >
        Hydorcele
        &nbsp;&nbsp;
        Other
        <input name="mscrotum_5_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="mscrotum_5_t_type" type="hidden" value="text">      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>EPIDIDIMIDES</td>
      <td class="TBcontent"><input name="mepid_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL
        &nbsp;&nbsp;
              <input name="mepid_1" type="checkbox" value="enlarges" onClick="CheckboxRule(name,checked)" >
        Enlarges
        &nbsp;&nbsp;
              <input name="mepid_2" type="checkbox" value="indurated" onClick="CheckboxRule(name,checked)" >
        Indurated
        &nbsp;&nbsp;
              <input name="mepid_3" type="checkbox" value="tender" onClick="CheckboxRule(name,checked)" >
        Tender
        &nbsp;&nbsp;
              <input name="mepid_4" type="checkbox" value="mass" onClick="CheckboxRule(name,checked)" >
        Mass
        &nbsp;&nbsp;
        Other
        <input name="mepid_5_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="mepid_5_t_type" type="hidden" value="text"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>TESTES</td>
      <td class="TBcontent"><input name="testes_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL
        &nbsp;&nbsp;
              <input name="testes_1" type="checkbox" value="tenderness" onClick="CheckboxRule(name,checked)" >
        Tenderness
        &nbsp;&nbsp;
              <input name="testes_2" type="checkbox" value="symmetry" onClick="CheckboxRule(name,checked)">
        Symmetry
        &nbsp;&nbsp;
              <input name="testes_3" type="checkbox" value="mass" onClick="CheckboxRule(name,checked)" >
        Mass
        &nbsp;&nbsp;
        Other
        <input name="testes_4_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="testes_4_t_type" type="hidden" value="text">      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>MEATUS</td>
      <td class="TBcontent"><input name="mureth_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL
        &nbsp;&nbsp;
              <input name="mureth_1" type="checkbox" value="small" onClick="CheckboxRule(name,checked)" >
        Small
        &nbsp;&nbsp;
              <input name="mureth_2" type="checkbox" value="large" onClick="CheckboxRule(name,checked)" >
        Large
        &nbsp;&nbsp;
              <input name="mureth_3" type="checkbox" value="polyp" onClick="CheckboxRule(name,checked)" >
        Polyp
        &nbsp;&nbsp;
              <input name="mureth_4" type="checkbox" value="hyposadius" onClick="CheckboxRule(name,checked)" >
        Hyposadius
        &nbsp;&nbsp;
        Other
        <input name="mureth_5_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="mureth_5_t_type" type="hidden" value="text">      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>PENIS</td>
      <td class="TBcontent"><input name="penis_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL
        &nbsp;&nbsp;
              <input name="penis_1" type="checkbox" value="phimosis" onClick="CheckboxRule(name,checked)" >
        Phimosis
        &nbsp;&nbsp;
              <input name="penis_2" type="checkbox" value="peyronie's" onClick="CheckboxRule(name,checked)" >
        Peyronie's
        &nbsp;&nbsp;
              <input name="penis_3" type="checkbox" value="condyloma" onClick="CheckboxRule(name,checked)" >
        Condyloma
        &nbsp;&nbsp;
              <input name="penis_4" type="checkbox" value="lump" onClick="CheckboxRule(name,checked)" >
        Lump
        &nbsp;&nbsp;
        Other
        <input name="penis_5_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="penis_5_t_type" type="hidden" value="text"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>PROSTATE</td>
      <td class="TBcontent"><input name="prostate_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)">
        NL
        &nbsp;&nbsp;
              <input name="prostate_1" type="checkbox" value="tender" onClick="CheckboxRule(name,checked)" >
        Tender
        &nbsp;&nbsp;
              <input name="prostate_2" type="checkbox" value="nodular" onClick="CheckboxRule(name,checked)" >
        Nodular
          
        &nbsp;&nbsp;
              <input name="prostate_3" type="checkbox" value="firm" onClick="CheckboxRule(name,checked)" >
        Firm
        &nbsp;&nbsp;
              <input name="prostate_4" type="checkbox" value="hard" onClick="CheckboxRule(name,checked)" >
        Hard
        &nbsp;&nbsp;
        Size(g)
        <input name="prostate_5_t" type="textbox" value="" oninput="CheckboxRule(name,value)" size="2"  >
              <input name="prostate_5_t_type" type="hidden" value="numeric">
        &nbsp;&nbsp;
        Other
        <input name="prostate_6_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="prostate_6_t_type" type="hidden" value="text">      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>SEMINAL VESICLES</td>
      <td class="TBcontent"><input name="sem_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL
        &nbsp;&nbsp;
              <input name="sem_1" type="checkbox" value="hard" onClick="CheckboxRule(name,checked)" >
        Hard
        &nbsp;&nbsp;
              <input name="sem_2" type="checkbox" value="indurated" onClick="CheckboxRule(name,checked)" >
        Indurated
        &nbsp;&nbsp;
        Other
        <input name="sem_3_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="sem_3_t_type" type="hidden" value="text">      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>SPINCTER TONE</td>
      <td class="TBcontent"><input name="spincter_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL
        &nbsp;&nbsp;
              <input name="spincter_1" type="checkbox" value="poor" onClick="CheckboxRule(name,checked)" >
        Poor
        &nbsp;&nbsp;
              <input name="spincter_2" type="checkbox" value="hemorrhoids" onClick="CheckboxRule(name,checked)" >
        Hemorrhoids
        &nbsp;&nbsp;
              <input name="spincter_3" type="checkbox" value="mass" onClick="CheckboxRule(name,checked)" >
        Mass
        &nbsp;&nbsp;
        Other
        <input name="spincter_4_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="spincter_4_t_type" type="hidden" value="text"></td>
    </tr>
    <tr>
      <td><strong>FEMALE 7 OF 11</strong></td>
      <td>BREAST</td>
      <td class="TBcontent"><input name="breast_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL
        &nbsp;&nbsp;
              <input name="breast_1" type="checkbox" value="symmetrical" onClick="CheckboxRule(name,checked)" >
        Symmetrical
        &nbsp;&nbsp;
              <input name="breast_2" type="checkbox" value="tender" onClick="CheckboxRule(name,checked)" >
        Tender
        &nbsp;&nbsp;
              <input name="breast_3" type="checkbox" value="mass" onClick="CheckboxRule(name,checked)" >
        Mass
        &nbsp;&nbsp;
              <input name="breast_4" type="checkbox" value="discharge" onClick="CheckboxRule(name,checked)" >
        Discharge
        &nbsp;&nbsp;
        Other
        <input name="breast_5_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="breast_5_t_type" type="hidden" value="text">      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>RECTAL EXAM </td>
      <td class="TBcontent"><input name="dre_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL
        &nbsp;&nbsp;
              <input name="dre_1" type="checkbox" value="tone" onClick="CheckboxRule(name,checked)" >
        Tone
        &nbsp;&nbsp;
              <input name="dre_2" type="checkbox" value="mass" onClick="CheckboxRule(name,checked)" >
        Mass
        &nbsp;&nbsp;
              <input name="dre_3" type="checkbox" value="hemorrhoids" onClick="CheckboxRule(name,checked)" >
        Hemorrhoids
        &nbsp;&nbsp;
        Other
        <input name="dre_4_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="dre_4_t_type" type="hidden" value="text">      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>GENITALIA</td>
      <td class="TBcontent"><input name="ext_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL
        &nbsp;&nbsp;
              <input name="ext_1" type="checkbox" value="lesion" onClick="CheckboxRule(name,checked)" >
        Lesion
        &nbsp;&nbsp;
              <input name="ext_2" type="checkbox" value="caruncle" onClick="CheckboxRule(name,checked)" >
        Caruncle
        &nbsp;&nbsp;
              <input name="ext_3" type="checkbox" value="condyloma" onClick="CheckboxRule(name,checked)" >
        Condyloma
        &nbsp;&nbsp;
              <input name="ext_4" type="checkbox" value="rash" onClick="CheckboxRule(name,checked)" >
        Rash
        &nbsp;&nbsp;
        Other
        <input name="ext_5_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="ext_5_t_type" type="hidden" value="text"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>MEATUS</td>
      <td class="TBcontent"><input name="urethmeat_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL  &nbsp;&nbsp;
        <input name="urethmeat_1" type="checkbox" value="prolapse" onClick="CheckboxRule(name,checked)" >
        Prolapse
        &nbsp;&nbsp;
              <input name="urethmeat_2" type="checkbox" value="small" onClick="CheckboxRule(name,checked)">
        Small
        &nbsp;&nbsp;
              <input name="urethmeat_3" type="checkbox" value="large" onClick="CheckboxRule(name,checked)" >
        Large
        &nbsp;&nbsp;
        Other
        <input name="urethmeat_4_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="urethmeat_4_t_type" type="hidden" value="text">      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>URETHRA</td>
      <td class="TBcontent"><input name="furethra_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL
        &nbsp;&nbsp;
              <input name="furethra_1" type="checkbox" value="tender" onClick="CheckboxRule(name,checked)" >
        Tender
        &nbsp;&nbsp;
              <input name="furethra_2" type="checkbox" value="masses" onClick="CheckboxRule(name,checked)" >
        Masses
        &nbsp;&nbsp;
        Other
        <input name="furethra_3_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="furethra_3_t_type" type="hidden" value="text"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>BLADDER</td>
      <td class="TBcontent"><input name="fbladder_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL
        &nbsp;&nbsp;
              <input name="fbladder_1" type="checkbox" value="mass" onClick="CheckboxRule(name,checked)" >
        Mass
        &nbsp;&nbsp;
              <input name="fbladder_2" type="checkbox" value="tender" onClick="CheckboxRule(name,checked)" >
        Tender
        &nbsp;&nbsp;
        Other
        <input name="fbladder_3_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="fbladder_3_t_type" type="hidden" value="text"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>VAGINA</td>
      <td class="TBcontent"><input name="vagina_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL
        &nbsp;&nbsp;
              <input name="vagina_1" type="checkbox" value="cystocele" onClick="CheckboxRule(name,checked)" >
        Cystocele
        &nbsp;&nbsp;
              <input name="vagina_2" type="checkbox" value="rectocele" onClick="CheckboxRule(name,checked)" >
        Rectocele
        &nbsp;&nbsp;
              <input name="vagina_3" type="checkbox" value="enterocele" onClick="CheckboxRule(name,checked)" >
        Enterocele
        &nbsp;&nbsp;
        Other
        <input name="vagina_4_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="vagina_4_t_type" type="hidden" value="text">      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>CERVIX</td>
      <td class="TBcontent"><input name="cervix_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL
        &nbsp;&nbsp;
              <input name="cervix_1" type="checkbox" value="inflamed" onClick="CheckboxRule(name,checked)" >
        Inflamed
        &nbsp;&nbsp;
              <input name="cervix_2" type="checkbox" value="discharge" onClick="CheckboxRule(name,checked)" >
        Discharge
        &nbsp;&nbsp;
        Other
        <input name="cervix_3_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="cervix_3_t_type" type="hidden" value="text">      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>UTERUS</td>
      <td class="TBcontent"><input name="futerus_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL
        &nbsp;&nbsp;
              <input name="futerus_1" type="checkbox" value="mass" onClick="CheckboxRule(name,checked)" >
        Mass
        &nbsp;&nbsp;
              <input name="futerus_2" type="checkbox" value="tender" onClick="CheckboxRule(name,checked)" >
        Tender
        &nbsp;&nbsp;
        Other
        <input name="futerus_3_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="futerus_3_t_type" type="hidden" value="text">      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>ADNEXA</td>
      <td class="TBcontent"><input name="adnexa_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL
        &nbsp;&nbsp;
              <input name="adnexa_1" type="checkbox" value="tenderness" onClick="CheckboxRule(name,checked)" >
        Tenderness
        &nbsp;&nbsp;
              <input name="adnexa_2" type="checkbox" value="mass" onClick="CheckboxRule(name,checked)" >
        Mass
        &nbsp;&nbsp;
        Other
        <input name="adnexa_3_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="adnexa_3_t_type" type="hidden" value="text">      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>ANUS/PERINEUM</td>
      <td class="TBcontent"><input name="fanus_0" type="checkbox" value="normal" onClick="CheckboxRule(name,checked)" >
        NL
        &nbsp;&nbsp;
              <input name="fanus_1" type="checkbox" value="fissures" onClick="CheckboxRule(name,checked)" >
        Fissures
        &nbsp;&nbsp;
              <input name="fanus_2" type="checkbox" value="edema" onClick="CheckboxRule(name,checked)" >
        Edema
        &nbsp;&nbsp;
              <input name="fanus_3" type="checkbox" value="tenderness" onClick="CheckboxRule(name,checked)" >
        Tenderness
        &nbsp;&nbsp;
        Other
        <input name="fanus_4_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
              <input name="fanus_4_t_type" type="hidden" value="text">      </td>
    </tr>
    <tr> </tr>
  <td colspan="3">&nbsp;</td>
  <tr>
    <td colspan="3" align="center"><input name="submit" type="submit" value="Submit"></td>
  </tr>
  </table>
  <span class="TBcontent">
  <input name="hernia_5_t" type="textbox" value="" oninput="CheckboxRule(name,value)" >
  </span>
</form>
</body>
</html>