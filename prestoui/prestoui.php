<html>
<head>
<title>PrestoDb QueryRunner</title>
<style>
table {
    border-collapse: collapse;
}

table, td, th {
    border: 1px solid #595959;
}
th.uppercase {
    text-transform: uppercase;
}
</style>

</head>

<h2>Hadoop Query UI</h2><h4> Don't run another query if one is running.. And don't include ';' at the end of Query.. Select 'Download Result in Excel' if need result in excel sheet. Thanks</h4>

<form enctype="multipart/form-data" action="" method="POST">
    <textarea name="example" rows="15" cols="120" placeholder="One quey only"> <?php if(isset($_POST['example'])) {
         echo htmlentities ($_POST['example']); }?></textarea><br>
<input type="radio" name="opt" value="print" checked> Only Show Result on Browser<br>
<input type="radio" name="opt" value="downld"> Download Result in Excel<br>
<input class="btn btn-success" type="submit" value="Run Query">
<input class="btn btn-success" type="submit" name="cancel" value="cancel">

</form>

<dl>
  <dt>Guide:</dt>
  <dd>- History: select query_id,state,started,query from system.runtime.queries order by query_id desc limit 10</dd>
  <dd>- Kill:    system.runtime.kill_query('query_id')</dd>
  <dd>- <a href="https://prestodb.io/docs/current/" target="_blink">Document</a></dd>
  <dd>- select query_id,state,started,query from system.runtime.queries where state='RUNNING'</dd>
  <dd>- </dd>
</dl>
</html>
<?php
//Developer TL: DInesh K Rajput(dkrajput.it@gmail.com)
function contains($str, array $arr)
{
    foreach($arr as $a) {
        if (stripos($str,$a) !== false) {echo "DDL Operation: ". $a . "<br>";return true;}
    }
    return false;
}
?>

<?php
use \Xtendsys\PrestoClient;
use \Xtendsys\PrestoException;
require_once(__DIR__ . '/src3/PrestoClient.php');
$presto = new PrestoClient("http://hadoop1:10000/v1/statement","hive");

if($_POST['example'] != ''){
//if(trim($_POST['opt'])='print'){
$query= $_POST['example'];
echo "Result for: $query;";
echo "</br>";

try {
$presto->Query("$query");
} catch (PrestoException $e) {
var_dump($e);
}
//Execute the request and build the result
$presto->WaitQueryExec();

//Get the result
$answer1 = $presto->GetColumns();
$answer = $presto->GetData();


if($_POST['opt'] != 'print')
{echo "Excel File ";

$ep = "\t"; //tabbed character
$fpath="/rpt/";
$rpath="/imart2/cdh/uipresto/rpt/";
$current=time()."data.xls";
$file=$rpath.$current;
$fp = fopen($file, "w");
$schema_insert = "";
$schema_insert_rows = "";


foreach($answer1 as $row)
{
//$schema_insert_rows.=$key . "\t";
$schema_insert_rows.=strtoupper($row->name)."\t";
}
$schema_insert_rows.="\n";

foreach($answer as $row)
{
foreach($row as $key=> $val1)
{
$val2=htmlentities($val1);
 $schema_insert_rows.="$val2"."\t";
}
$schema_insert_rows.="\n";
}
$schema_insert_rows.="\n";
fwrite($fp, $schema_insert_rows);
//end of adding column names
$fdow=$fpath.$current;
echo "<a href=$fdow download>Download</a>";
echo "</br>";
fclose($file);
//table with limit 10;

echo "Row Fetched: ";echo (sizeof($answer));
echo "</br>";
echo "<table border='1'>";
foreach($answer1 as $k)
{
$val=strtoupper($k->name);
echo "<th> $val </th>";
}

echo "</br>";
$i=0;

foreach($answer as $row)
{$i++;
echo"<tr>";
foreach($row as $key=> $val)
{
    #echo $key . ' = ' . $var . '<br />';
//echo "<tr> $val </tr>";
echo "<td> $val </td>";
}
if($i==10)break;
echo "</tr>";
}
echo "</table>";

}
else{echo "";

//table print
echo "Row Fetched: ";echo (sizeof($answer));
echo "</br>";
echo "<table border='1'>";
foreach($answer1 as $k)
{
$val=strtoupper($k->name);
echo "<th> $val </th>";
}

echo "</br>";
foreach($answer as $row)
{
echo"<tr>";
foreach($row as $key=> $val)
{
    #echo $key . ' = ' . $var . '<br />';
//echo "<tr> $val </tr>";
echo "<td> $val </td>";
}
echo "</tr>";
}
echo "</table>";
}
//}
}
?>
