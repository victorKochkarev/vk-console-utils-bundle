<?php

namespace VK\CliUtils\Helper;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class QuestionArrayObjectSelectorUnique
{
    public static function selectObjectFromArrayQuestion(InputInterface $input, OutputInterface $output, $itemList, string $displayPropertyName, string $questionText, $multipleSelect = false){
        if(count($itemList) == 0){
            throw new InvalidArgumentException('Item List is empty');
        }

        $questionHelper = new QuestionHelper();
        $nameList = [];
        $nameGetterName = 'get' . ucfirst($displayPropertyName);
        foreach ($itemList as $item){
            $nameList[] = $item->$nameGetterName();
        }

        if(count(array_unique($nameList)) != count($nameList)){
            throw new \Exception('Input list items have duplicated names');
        }

        $question = new ChoiceQuestion(
            $questionText,
            $nameList
        );

        $question->setMultiselect($multipleSelect);

        $selectedObjectName = $questionHelper->ask($input, $output, $question);
        $selectedObjectNameList = [];
        if(is_array($selectedObjectName)){
            $selectedObjectNameList = $selectedObjectName;
        }else{
            $selectedObjectNameList = [$selectedObjectName];
        }

        if($multipleSelect){
            $result = [];
        }else{
            $result = null;
        }

        foreach ($itemList as $item){
            if(in_array($item->$nameGetterName(), $selectedObjectNameList)){
                if($multipleSelect){
                    $result[] = $item;
                }else{
                    $result = $item;
                    break;
                }
            }
        }

        return $result;
    }
}