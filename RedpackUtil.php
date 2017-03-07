<?php
/**
 * Created by PhpStorm.
 * User: biken
 * Date: 2017/3/7
 * Time: 15:22
 */
class RedpackUtil
{
    /**
     *
     * @param $bonusTotal 红包总额
     * @param $bonusCount 红包个数
     * @param $bonusMax 每个小红包的最大额
     * @param $bonusMin 每个小红包的最小额
     * @return array 存放生成的每个小红包的值的一维数组
     *
     * 此算法要求设置的红包最大值要大于红包平均值
     */
    public static function getRedpack($bonusTotal, $bonusCount, $bonusMax, $bonusMin)
    {
        $result = array();
        $average = $bonusTotal / $bonusCount;  //平均金额
        //$a = $average - $bonusMin;   //平均金额-最小金额
        //$b = $bonusMax - $bonusMin;  //最大金额-平均值

        //这样的随机数的概率实际改变了，产生大数的可能性要比产生小数的概率要小。
        //这样就实现了大部分红包的值在平均数附近。大红包和小红包比较少。
        //$range1 = self::sqr($average - $bonusMin);
        //$range2 = self::sqr($bonusMax - $average);
        for ($i = 0;  $i < $bonusCount;  $i++) {
            //因为小红包的数量通常是要比大红包的数量要多的，因为这里的概率要调换过来
            //当随机数>平均值，则产生小红包
            //当随机数<平均值，则产生大红包
            if (mt_rand($bonusMin, $bonusMax) > $average) {
                // 在平均线上减钱  生成小红包
                $temp = $bonusMin + self::xRandom($bonusMin, $average);
                $result[$i] = $temp;
                $bonusTotal -= $temp;
            } else{
                // 在平均线上加钱  生成大红包
                $temp = $bonusMax - self::xRandom($average, $bonusMax);
                $result[$i] = $temp;
                $bonusTotal -= $temp;
            }
        }

        // 如果还有余钱，则尝试加到小红包里，如果加不进去，则尝试下一个。
        while ($bonusTotal > 0) {
            for ($i = 0; $i < $bonusCount; $i++) {
                if ($bonusTotal > 0 && $result[$i] < $bonusMax) {
                    $diff = $bonusMax - $result[$i];
                    if ($diff >= 2) {
                        $diff = self::xRandom(0, $diff);
                    }
                    $result[$i] += $diff;
                    $bonusTotal -= $diff;
                }
            }
        }
        // 如果钱是负数了，还得从已生成的小红包中抽取回来
        while ($bonusTotal < 0) {
            for ($i = 0; $i < $bonusCount; $i++) {
                if ($bonusTotal < 0 && $result[$i] > $bonusMin) {
                    $diff = $result[$i] - $bonusMin;
                    if ($diff >= 2) {
                        $diff = self::xRandom(0, $diff);
                    }

                    $result[$i] -= $diff;
                    $bonusTotal += $diff;
                }
            }
        }

        while ($bonusTotal > 0) {
            for ($i = 0; $i < $bonusCount; $i++) {
                if ($bonusTotal > 0 && ($result[$i] + $bonusTotal)  < $bonusMax) {
                    $result[$i] = sprintf("%.2f",$result[$i] + $bonusTotal);
                    $bonusTotal = 0;
                }
            }
        }

        shuffle($result);
        return $result;
    }

    /**
     * 求一个数的平方
     * @param $n
     */
    static function sqr($n){
        return $n*$n;
    }

    /**
     * 生产min和max之间的随机数，但是概率不是平均的，从min到max方向概率逐渐加大。
     * 先平方，然后产生一个平方值范围内的随机数，再开方，这样就产生了一种“膨胀”再“收缩”的效果。
     */
    static function xRandom($bonusMin,$bonusMax){
        $sqr = intval(self::sqr($bonusMax-$bonusMin));
        $rand_num = rand(0, ($sqr-1));
        return  sprintf("%.2f",sqrt($rand_num));
    }
}

$bonusTotal = 100;
$bonusCount = 90;
$bonusMax = 6.99;//此算法要求设置的最大值要大于平均值
$bonusMin = 1;

$class = new RedpackUtil();
$redpackList = $class::getRedpack($bonusTotal, $bonusCount, $bonusMax, $bonusMin);

var_dump(array_sum($redpackList));
var_dump($redpackList);
