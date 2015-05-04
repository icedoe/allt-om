<?php

namespace Phpmvc\Comment;

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

    public function findAll($commenter='comment')
    {
        return $this->query()->where("commenter = $commenter")->execute();
    }

    public function save($values=[], $key='id')
    {
        if(isset($values['id']) && $values['id'] == $this['id']) {
            $values['updated'] = $values['timestamp'];
        }else{
            $values['created'] = $values['timestamp'];
        }
        unset($values['timestamp']);
        return parent::save($values, $key);
    }

    public function delete($value, $key='id')
    {
        return parent::delete($value, $key);
    }
}
