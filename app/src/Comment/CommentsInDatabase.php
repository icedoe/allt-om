<?php

namespace Deg\Comment;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class CommentsInDatabase extends \Anax\MVC\CDatabaseModel
{
    public function find($id, $key='id')
    {
        return parent::find($id, $key);
    }

    public function findAll($commenter='commenter')
    {
        $this->query()->where("commenter = $commenter")->execute();
    }

    public function findByTag($tag)
    {
        $pre ='phpmvc_project_';
        $sql ="SELECT ".$pre."commentsindatabase.*, ".$pre."User.image, ".$pre."User.type as authortype
                FROM phpmvc_project_commentsindatabase
                JOIN ".$pre."user
                ON ".$pre."commentsindatabase.author=".$pre."user.acronym
                WHERE tags LIKE '%,".$tag.",%';";
        $this->db->execute($sql);
        return $this->db->fetchAll();
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
        $pre ='phpmvc_project_';
        $sql ="SELECT ".$pre."commentsindatabase.*, ".$pre."User.image, ".$pre."User.type as authortype
                FROM phpmvc_project_commentsindatabase
                JOIN ".$pre."user
                ON ".$pre."commentsindatabase.author=".$pre."user.acronym
                WHERE ".$pre."commentsindatabase.type = 'question'";
        if($user){
            $sql .=" AND author = '".$user."'";
        }

        $sql .=";";

        $this->db->execute($sql);
        return $this->db->fetchAll();
    }

    public function findFullDisplay($id)
    {
        $pre ='phpmvc_project_';
        $sql ="SELECT ".$pre."commentsindatabase.*, ".$pre."User.image, ".$pre."User.type as authortype
                FROM ".$pre."commentsindatabase
                JOIN ".$pre."user
                ON author=acronym
                WHERE ".$pre."commentsindatabase.id = ".$id."
                OR (
                    forid = ".$id."
                    AND ".$pre."commentsindatabase.type = 'answer');";

        $str =$id;

        $this->db->execute($sql);
        $qares = $this->db->fetchAll();

        foreach($qares as $obj) {
            if($obj->id != $id){
                $str .=','.$obj->id;
            }
        }
        $sql ="SELECT ".$pre."commentsindatabase.*, ".$pre."User.image, ".$pre."User.type as authortype
                FROM ".$pre."commentsindatabase
                JOIN ".$pre."user
                ON author=acronym
                WHERE forid IN (".$str.")
                AND ".$pre."commentsindatabase.type = 'comment';";
        $this->db->execute($sql);
        $qares = array_merge($qares, $this->db->fetchAll());
        print_r($qares);
        $qares =$this->qaSort($qares);
        return $qares;
    }

    public function getColVals($col)
    {
        $this->query($col);
        $vals =$this->db->executeFetchAll();
        
        if($col == 'tags'){
            return $this->tagSort($vals);
        }
        return $vals;
    }

    public function cleanTags($vals)
    {
        $vals =str_replace(['å', 'ä', 'ö'], ['a', 'a', 'o'], strtolower($vals));
        $vals =explode(',', $vals);
        foreach($vals as &$val){
            trim($val);
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
        return $array;
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
        $pre ='phpmvc_project_';
        $col =$type.'count';
        $sql ="UPDATE ".$pre."commentsindatabase
                SET
                    ".$col." = ifnull(".$col.", 0) +1
                WHERE id =".$id.";";
        $this->db->execute($sql);
    }
}
