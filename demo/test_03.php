<?php

include_once('../vendor/autoload.php');
include_once('../src/KDB.php');
include_once('../src/KTag.php');
include_once('../src/KVar.php');
include_once('../src/KFormula.php');
include_once('../src/KException.php');
include_once('../src/KNode.php');

use xcranium\kdb\KDB;

KDB::createNewDB("teste1")
->addKTag("deal")
->addKTag("grant")
->addKVar("idade", ["formula" => '@current_date_sub($data_nascimento)', 'tags'=>['deal']])
->addKVar("data_nascimento", ["title" => "Data de nascimento", "type" => "date"])
->addKVar("elegivel_desconto",["tags" => ["deal"], "title" => "Elegivel ao desconto", "formula" => '($idade > 60)'])
->addKVar("eh_cidadao",["title" => "Voce eh cidadao?", "type"=>"yes_no"])
->addKVar("elegivel_grant",["title"=>"Elegivel a um grant", "tags"=>["grant"], "formula" => ' ( ($idade > 65) AND ($eh_cidadao == \'yes\') )' ])
->updateIndex();



$node1 = new xcranium\kdb\KNode(KDB::get("teste1"), "12345");

$node1->getKDB()->setValue('data_nascimento', '1950-05-21');

//$node1->getValue('elegivel_grant',true);

$node1->evaluate(false,true);

print_r($node1->getKDB()->getAllValues()); exit;

print_r($node1->evaluate());

print_r($node1->queryTag('deal'));

//$node1->dump();
exit;

//$node2 = new xcranium\kdb\KNode(KDB::get("teste1"), "456789");

//$node1-> 


$x = KDB::get("teste1");
echo "<pre>";
//$x->setValue('idade', 37);
$x->setValue('data_nascimento', '1950-05-21');

print_r($x->getMetaVar('idade'));
echo "<hr>";
print_r($x->queryTag('deal'));
echo "\n\n";
//print_r($x->getValue('elegivel_desconto',true));
print_r($x->getValue('elegivel_grant',true));
//print_r($x->getValue('idade',true));
echo "\n\n";
print_r($x->evaluate());
echo "\n\n\n\n";
print_r(\xcranium\kdb\KFormula::$vars);

$y = KDB::get("teste1")->getAllValues();
echo "\n\n\n\n";

print_r($y);

echo "\n\n =========================================== \n";

$x->setValue('eh_cidadao', 'yes');


print_r($x->evaluate());
echo "\n\n\n\n";
print_r(\xcranium\kdb\KFormula::$vars);

$y = KDB::get("teste1")->getAllValues();
echo "\n\n\n\n";

print_r($y);