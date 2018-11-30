<?php

namespace App\Foundations;

class SnowFlake{

    // 41 位、10 位、 12 位的最大值，作为掩码使用
    const MAX41BITS = 2199023255551;
    const MAX10BITS = 1023;
    const MAX12BITS = 4095;

    private $nodeId;

    private $seq;

    public function __construct()
    {
        $this->nodeId = config('app.snowflake.node_id');
        $this->seq = 0;
    }

    function snowId()
    {
        // 获取当前时间戳（毫秒）
        list($microsec, $sec) = explode(" ", microtime());
        $timestamp = $sec * 1000 + (int)($microsec * 1000);

        // 生成 id
        // 这里用到了位运算，稍微解释一下：
        // 先将时间戳与 41 位的最大值做一次与运算，保证其长度不会超过 41 位；
        // 再将节点号跟 10 位的最大值做与运算，也是保证其长度不会超过 10 位；
        // 然后将时间戳左移 22 位、节点号左移 12 位，这样就能使得时间戳在第 2 位到第 42 位、节点号在第 43 位到第 52 位；
        // 最后再对时间戳、节点号、自增序列做一次或运算，这样三个数字就拼接在一起了
        $id = ($timestamp & self::MAX41BITS) << 22 | ($this->nodeId & self::MAX10BITS) << 12 | $this->seq;

        // id 生成好之后将自增序列加 1 同时与 12 位的最大值做与运算，这样就能达到循环滚动的效果
        $this->seq = (++$this->seq) & self::MAX12BITS;

        return $id;
    }

    function parse($id)
    {
        $timestamp = ($id >> 22);
        $nodeId = ($id >> 12) & self::MAX10BITS;
        $seq = $id & self::MAX12BITS;
        return [$timestamp, $nodeId, $seq];
    }

}