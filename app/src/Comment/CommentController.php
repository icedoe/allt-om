<?php

namespace Deg\Comment;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class CommentController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable, \Anax\MVC\TRedirectHelpers;

    private $user;

    public function initialize()
    {
        $this->comments =new CommentsInDatabase();
        $this->comments->setDI($this->di);
        $this->user =$this->di->session->get('user');
    }


    public function indexAction($post=null, $calling='comment', $user=null)
    {
        $post =$post ? $post: $_POST;

        if(empty($user)){
            $this->theme->setTitle('Postade frågor');
        }
        
        $all = $this->comments->findForDisplay($user);
        
        foreach($all as &$q){
            $q->title =htmlentities($q->title);
            $q->author =htmlentities($q->author);
            $q->content =$this->di->textFilter->doFilter(htmlentities($q->content), 'markdown');
        }

        //
        

            $this->views->add('comment/questions', [
                'comments' => $all ],
                'main'
            );

            $this->di->dispatcher->forward([
            'controller' => 'sidebar',
            'action' => 'index',
            'params' => array($post, $calling, $user),
            ]);
    }

    public function startAction($post, $calling)
    {
        $all = $this->comments->findLatest(5);

        foreach($all as &$q){
            $q->title =htmlentities($q->title);
            $q->author =htmlentities($q->author);
            $q->content =$this->di->textFilter->doFilter(htmlentities($q->content), 'markdown');
        }

        $this->di->views->add('comment/questions', [
            'comments' => $all],
            'main'
        );

        $this->di->dispatcher->forward([
            'controller' => 'sidebar',
            'action' => 'index',
            'params' => [$post, $calling],
            ]);
    }

    public function viewAction($id)
    {
        $all = $this->comments->findFullDisplay($id);

        foreach($all as &$some){
            foreach($some as &$q){
            $q->title =htmlentities($q->title);
            $q->author =htmlentities($q->author);
            $q->content =$this->di->textFilter->doFilter(htmlentities($q->content), 'markdown');
        }
        }

        $this->di->theme->setTitle('Fråga: '.$id);
        $this->views->add('comment/comments', [
            'comments' => $all ],
            'main'
        );
        $this->di->dispatcher->forward([
            'controller' => 'sidebar',
            'action' => 'index',
            'params' => array($_POST),
            ]);
    }

    public function tagAction($tag)
    {
        $all =$this->comments->findByTag($tag);
        $this->di->theme->setTitle("Frågor med tag: $tag");

        $this->views->add('comment/questions', [
            'comments' => $all],
            'main'
            );
        $this->dispatcher->forward([
            'controller' => 'sidebar',
            'action' => 'index',
            'params' => array($_POST),
            ]);
    }

    /**
     * Controller function
     *
     */
    public function editAction($type='question', $forId=null)
    {
        $id =$this->di->request->getPost('id');
        

        $comment =$id ? $this->comments->find($id) : $this->comments;
        $idComment = $comment->getProperties();

        $array = [];

        $array['type'] = [
            'type'      => 'hidden',
            'value'     => $type,
            ];

        if($type == 'question' || $type == 'answer'){
            $array['title'] = [
                'type'      =>'text',
                'required'  =>true,
            ];
            if($type == 'question'){
                $array['tags'] = [
                    'type'  =>'text',
                ];
            }
        }

        if($type == 'answer' || $type == 'comment'){
            $array['forid'] = [
                'type'      => 'hidden',
                'value'     => $forId,
                ];
        }
        
        $array['content'] = [
            'type'      => 'textarea',
            'validation'    => ['not_empty'],
            'required'  => true,
            ];
        
        $array['save'] = [
            'type'      => 'submit',
            'value'     => 'Spara',
            'callback'  => function($form) {
                                $form->addOutput("Kommentar: Spara");
                                $form->doValidate=true;
                                return 'save';
                            },
            ];


            $form =$this->di->form->create(['class' => 'comment-form'], $array);

            $action =$form->check();
            switch($action){
                case 'save':
                    $now =gmdate('Y-m-d H:i:s');
                    $values = [
                        'author'    =>$this->user['acronym'],
                        'content'   => $form->Value('content'),
                        'type' => $form->Value('type'),
                        'forid'     => $form->Value('forid'),
                        'created' => $now,
                    ];
                    if($form->Value('title')){
                        $values['title'] =$form->Value('title');
                    }
                    if($form->Value('tags')){
                        $values['tags'] =$comment->cleanTags($form->Value('tags'));
                    }

                    if($comment->save($values)){
                        $done =true;
                        $this->user['posted'] =empty($this->user['posted']) ? 1 : $this->user['posted']+1;
                        foreach($this->user as $key => $val){
                            if(empty($val)){
                                unset($this->user[$key]);
                            }
                        }
                        $this->di->users->save($this->user);
                        $this->di->session->set('user', $this->user);
                        if(!empty($values['forid'])){
                            $comment->countUp($values['type'], $values['forid']);
                        }
                    }
                    break;

                default:
                    $form->addOutput('Kunde inte spara');
                    $form->saveInSession =true;
                    $done=false;
                    break;
            }
            if($done){
                $this->redirectTo($this->di->url->create('comment'));
            } 
            $this->di->theme->setTitle('Edit');
            $this->di->views->add('me/page', [
                'content' => $form->getHTML(),
                'id' => $id]);
        }



    public function setup()
    {
         $this->db->dropTableIfExists('commentsindatabase')->execute();
 
        $this->db->createTable(
            'commentsindatabase',
            [
                'id'        => ['integer', 'auto_increment', 'primary key', 'not null'],
                'title'     => ['varchar(80)'],
                'author'    => ['varchar(80)', 'not null'],
                'content'   => ['text', 'not null'],
                'tags'      => ['varchar(100)'],
                'type'      => ['varchar(80)', 'not null'],
                'commentcount' => ['integer'],
                'answercount' => ['integer'],
                'forid'     => ['integer'],
                'created' => ['datetime'],
            ]
        );
        if($this->db->execute()){
            return true;
        }
        return false;
        
    }
}
