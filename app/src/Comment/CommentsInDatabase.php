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
        $qares = array_merge($qares, $this->db->fetchall());
        print_r($qares);
        $qares =$this->qaSort($qares);
        return $qares;
    }

    private function qaSort($qares)
    {
        $qa =[];
        foreach($qares as $obj){
            if($obj->type != 'comment'){
                $qa[$obj->id] =[$obj];
            }else{
                $qa[$obj->forid][] = $obj;
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
