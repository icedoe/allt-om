<?php

namespace Deg\Comment;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class CommentsInDatabase extends \Anax\MVC\CDatabaseModel
{
    private $pre;
    public function __construct(){
        $this->pre ='test_';
    }
    public function find($id, $key='id')
    {
        return parent::find($id, $key);
    }

    public function findAll($commenter='commenter')
    {
        $this->query()->where("commenter = $commenter")->execute();
    }

    public function save($values=[], $key='id')
    {
        return parent::save($values, $key);
    }

    public function update($values, $key='id')
    {
        return parent::update($values, $key);
    }

    public function delete($value, $key='id')
    {
        return parent::delete($value, $key);
    }

    public function findForDisplay($user=null)
    {
        $sql ="SELECT ".$this->pre."commentsindatabase.*, ".$this->pre."user.image, ".$this->pre."user.type as authortype
                FROM ".$this->pre."commentsindatabase
                JOIN ".$this->pre."user
                ON ".$this->pre."commentsindatabase.author=".$this->pre."user.acronym
                WHERE ".$this->pre."commentsindatabase.type = 'question'";
        if($user){
            $sql .=" AND author = '".$user."'";
        }

        $sql .=";";

        $this->db->execute($sql);
        $all = $this->db->fetchAll();
        foreach($all as &$post)
        {
            if(empty($post->tags)){
                continue;
            }
            $post->tags = $this->tagSort([$post]);
        }
        return $all;
    }

    public function findLatest($limit)
    {
        $limit =$limit ? " LIMIT ".$limit : '';
        $sql ="SELECT ".$this->pre."commentsindatabase.*, ".$this->pre."user.image, ".$this->pre."user.type as authortype
                FROM ".$this->pre."commentsindatabase
                JOIN ".$this->pre."user
                ON ".$this->pre."commentsindatabase.author=".$this->pre."user.acronym
                WHERE ".$this->pre."commentsindatabase.type = 'question'
                ORDER BY created DESC"
                .$limit.";";

        $this->db->execute($sql);
        $all = $this->db->fetchAll();
        foreach($all as &$post)
        {
            if(empty($post->tags)){
                continue;
            }
            $post->tags = $this->tagSort([$post]);
        }
        return $all;
    }

    public function findFullDisplay($id)
    {
        $sql ="SELECT ".$this->pre."commentsindatabase.*, ".$this->pre."user.image, ".$this->pre."user.type as authortype
                FROM ".$this->pre."commentsindatabase
                JOIN ".$this->pre."user
                ON author=acronym
                WHERE ".$this->pre."commentsindatabase.id = ".$id."
                OR (
                    forid = ".$id."
                    AND ".$this->pre."commentsindatabase.type = 'answer');";

        $str =$id;

        $this->db->execute($sql);
        $qares = $this->db->fetchAll();

        foreach($qares as $obj) {
            if($obj->id != $id){
                $str .=','.$obj->id;
            }
        }
        $sql ="SELECT ".$this->pre."commentsindatabase.*, ".$this->pre."user.image, ".$this->pre."user.type as authortype
                FROM ".$this->pre."commentsindatabase
                JOIN ".$this->pre."user
                ON author=acronym
                WHERE forid IN (".$str.")
                AND ".$this->pre."commentsindatabase.type = 'comment'
                ORDER BY id;";
        $this->db->execute($sql);
        $qares = array_merge($qares, $this->db->fetchAll());
        $qares =$this->qaSort($qares);
        foreach($qares as &$qa){
            foreach($qa as &$post){
                if(empty($post->tags)){
                    continue;
                }
                $post->tags = $this->tagSort([$post]);
            }
        }
        return $qares;
    }

    public function getColVals($col, $sort=true)
    {
        $this->query($col);
        $vals =$this->db->executeFetchAll();
        
        if($col == 'tags' && $sort){
            return $this->tagSort($vals);
        }
        return $vals;
    }

    public function findByTag($tag)
    {
        $sql ="SELECT ".$this->pre."commentsindatabase.*, ".$this->pre."user.image, ".$this->pre."user.type as authortype
                FROM ".$this->pre."commentsindatabase
                JOIN ".$this->pre."user
                ON ".$this->pre."commentsindatabase.author=".$this->pre."user.acronym
                WHERE tags LIKE '%,".$tag.",%';";
        $this->db->execute($sql);
        $all = $this->db->fetchAll();
        foreach($all as &$post)
        {
            if(empty($post->tags)){
                continue;
            }
            $post->tags = $this->tagSort([$post]);
        }
        return $all;
    }

    public function mostPopularTags($limit)
    {
        $tagArray = $this->getColVals('tags', false);
        $counter =[];
        foreach($tagArray as $tags){
            $tmp =explode(',', $tags->tags);
            foreach($tmp as $tag){
                if(!isset($counter[$tag]) && !empty($tag)){
                    $counter[$tag] =0;
                }
                if(!empty($tag)){
                    $counter[$tag] += 1;
                }
            }
        }
        arsort($counter);
        return array_keys(array_slice($counter, 0, $limit, true));
    }

    public function cleanTags($vals)
    {
        $vals =str_replace(['å', 'ä', 'ö'], ['a', 'a', 'o'], strtolower($vals));
        $vals =explode(',', $vals);
        foreach($vals as &$val){
            $val =trim($val);
        }
        return ','.implode(',', $vals).',';
    }

    private function tagSort($vals)
    {
        $array =[];
        foreach($vals as $obj){
            $tmp = explode(',', $obj->tags);
            foreach($tmp as $val){
                $val =trim($val);
                if(!empty($val) && !in_array($val, $array)){
                    $array[] =$val;
                }
            }
        }
        return arsort($array);
    }

    private function qaSort($qares)
    {
        $qa =[];
        foreach($qares as $obj){
            if($obj->type != 'comment'){
                $id =$obj->id;
                $qa[$id] =[$obj];
            }else{
                $id =$obj->forid;
                $qa[$id][] = $obj;
            }
        }
        return $qa;
    }

    public function countUp($type, $id)
    {
        $col =$type.'count';
        $sql ="UPDATE ".$this->pre."commentsindatabase
                SET
                    ".$col." = ifnull(".$col.", 0) +1
                WHERE id =".$id.";";
        $this->db->execute($sql);
    }
}
