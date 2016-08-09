 <?php

    const HOST   = 'localhost';
    const USER   = 'root';
    const PWD    = '';
    const CHAR   = 'utf8';
    const DBNAME = 's48_shop';


    /*
    |--------------------------------------------------------------------------
    | Medel 类
    |--------------------------------------------------------------------------
    |
    | author: TommyDai  email: 236860783@qq.com
    |
    | 数据库CURD操作
    | 支持连贯操作、支持传数组等动态接收数据
    | 
    | 
    |
    */

    class Model
    {
        private        $tableName;
        static private $link;
        private        $field;
        private        $data;
        private        $where = '';
        private        $order = '';
        private        $limit = '';

        //初始化
        public function __construct($tableName)
        {
            self::$link = self::link();
            $this->tableName = '`'.$tableName.'`';
            $this->field = $this->field();
        }

        //连接数据库
        static private function link()
        {
            $link = mysqli_connect(HOST,USER,PWD,DBNAME);
            if(mysqli_connect_errno($link))
                exit(mysqli_connect_error($link));
            mysqli_set_charset($link,CHAR);
            return $link;
        }

        //where条件
        public function where($where)
        {
            $this->where = empty($where)?:' WHERE '.$where;
            return $this;
        }

        //order by排序
        public function order($order)
        {
            $this->order = empty($order)?:' ORDER BY '.$order;
            return $this;
        }

        //limit分页
        public function limit($limit)
        {
            $this->limit = empty($limit)?:' limit '.$limit;
            return $this;
        }

        //总记录数
        public function count()
        {
            $sql = 'SELECT COUNT(*) FROM '.$this->tableName.$this->where;
            return (int) current($this->query($sql)[0]);
        }

        //查出所有数据
        public function select()
        {
            $sql = 'SELECT '.$this->field.
                   ' FROM '.$this->tableName
                   .$this->where
                   .$this->order
                   .$this->limit;
            $this->where = '';
            $this->order = '';
            $this->limit = '';
            return $this->query($sql);
        }

        //通过id查一条数据
        public function find($id)
        {
            $sql = 'SELECT '.$this->field.' FROM '.$this->tableName.
                   ' WHERE id='.$id;
            return $this->query($sql);
        }

        //插入数据
        public function add()
        {
            $key = implode(',',array_keys($this->data));
            $values = implode(',',array_values($this->data));
            $sql = 'INSERT INTO '.$this->tableName.
                   '('.$key.') VALUES('.$values.')';
            return $this->execute($sql);
        }

        //更新数据
        public function update($id)
        {
            $set = '';
            foreach($this->data as $key => $value)
                $set .= $key.'='.$value.',';
            $set = rtrim($set,',');
            $sql = 'UPDATE '.$this->tableName.
                   ' SET '.$set.' WHERE id='.$id;
            return $this->execute($sql);
        }

        //删除一条记录
        public function delete($id)
        {
            $sql = 'DELETE FROM '.$this->tableName.' WHERE id='.$id;
            return $this->execute($sql);
        }

        //获取表字段
        private function field()
        {
            $sql = 'DESC '.$this->tableName;
            $arr = $this->query($sql);
            foreach($arr as $val)
                $field[] = '`'.current($val).'`';
            return rtrim(implode(',',$field),',');
        }

        //赋值操作
        public function __set($name,$val)
        {
            if(is_array($val)){
                foreach($val as $k => $v){
                    $key = '`'.$k.'`';
                    $val = is_string($v) ? "'".$v."'" : $v;
                    $this->data[$key] = $val;
                }
                return;
            }
            $key = '`'.$name.'`';
            $value = is_string($val) ? "'".$val."'" : $val;
            $this->data[$key] = $value;
        }

        //处理查询结果集
        public function query($sql)
        {
            $res = mysqli_query(self::$link,$sql);
            if($res && mysqli_num_rows($res)>0){
                while($row = mysqli_fetch_assoc($res))
                    $arr[] = $row;
                return $arr;
            }
        }

        //处理增删改结果集
        public function execute($sql)
        {
            $res = mysqli_query(self::$link,$sql);
            $this->data = null;
            return mysqli_insert_id(self::$link)?
                mysqli_insert_id(self::$link):
                mysqli_affected_rows(self::$link);
        }

        //关闭数据库连接
        public function __destruct()
        {
            if(!empty(self::$link)) mysqli_close(self::$link);
        }
    }
