    <?php

    /*
    |--------------------------------------------------------------------------
    | 支持搜索的分页类
    |--------------------------------------------------------------------------
    |
    | author: TommyDai  email: 236860783@qq.com
    |
    | 代码极度精简，实现自动拼接url搜索分页
    | 
    | 
    |
    */

    class Page
    {
        public $limit;
        public $curr;
        public $end;
        public $url;

        public function __construct($total,$num)
        {
            $this->curr = isset($_GET['page'])?abs(round($_GET['page'])):1;
            $this->end = ceil($total/$num);
            $start = ($this->curr-1)*$num;
            $this->limit = $start.','.$num;
            parse_str($_SERVER['QUERY_STRING'],$arr);
            unset($arr['page']);
            $this->url = empty($arr) ? '' : '&'.http_build_query($arr);
        }

        public function show()
        {
            $first = '<a href="?page=1'.$this->url.'">首页</a>';
            $end = '<a href="?page='.$this->end.$this->url.'">末页</a>';
            $num = '';
            for($i=$this->curr-5;$i<$this->curr+6;$i++)
                if($i>0 && $i<=$this->end)
                    $num .= $i==$this->curr ?
                        '<span>'.$i.'</span>' :
                        '<a href="?page='.$i.$this->url.'">'.$i.'</a>';
            echo $first.$num.$end;
        }
    }
