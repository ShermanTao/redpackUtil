<?php
/**
 * Created by PhpStorm.
 * User: sherman
 * Date: 2017/3/7
 * Time: 16:59
 */

class NewRedpackUtil
{
    /**
     *
     * @param $bonusTotal 红包总额
     * @param $bonusCount 红包个数
     * @param $bonusMin 每个小红包的最小额
     * @return array 存放生成的每个小红包的值的一维数组
     *
     * 此算法无法限制小红包的最大金额
     */
    public static function getRedpack($bonusTotal, $bonusCount, $bonusMin)
    {
        $bonusTotal = intval($bonusTotal);
        $bonusCount = intval($bonusCount);

        $result = array();
        if ($bonusTotal <= 0 || $bonusCount < 0 || $bonusCount > $bonusTotal) {
            //不满足条件返回空数组
            return $result;
        }

        //除去每人最低保证的金额
        $randMoney = $bonusTotal - $bonusCount * $bonusMin;

        //刚好满足每人最低金额
        if ($randMoney == 0) {
            return array_pad($result, $bonusCount, $bonusMin);
        }

        //存放切割刻度 key表示第几个人，value表示随机到的刻度
        $randArr = array();
        for ($i=0; $i<$bonusCount-1; $i++) {
            //每人的随机范围都一样保证公平性
            $randArr[$i] = rand(0, $randMoney);
        }

        //最后一个人的刻度默认在最后，相当于最后一个人不用抽了
        $randArr[$bonusCount] = $randMoney;
        //按刻度进行排序，方便从前往后切段
        asort($randArr);

        //上一个切割点
        $lastValue = 0;
        foreach ($randArr as $key => $value) {
            //将切段分给对应的人
            $result[$key] = $bonusMin + $value - $lastValue;
            $lastValue = $value;
        }

        $result = array_values($result);
        shuffle($result);
        return $result;
    }
}

$redpackList = NewRedpackUtil::getRedpack(6, 5, 1.43);

var_dump(array_sum($redpackList));
var_dump($redpackList);
