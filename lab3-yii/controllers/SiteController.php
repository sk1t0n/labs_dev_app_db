<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Category;
use phpQuery;

use yii\helpers\Url;

require('phpQuery/phpQuery.php');
error_reporting(0);

class SiteController extends Controller
{
    public $defaultAction = 'index';

    private static function get_comment_users($html) {
        phpQuery::newDocument($html);
        $ul_comment_users = pq('div.question__comments>ul')->children('li');
        $comment_users = [];
        foreach ($ul_comment_users as $comment_user) {
            phpQuery::newDocument(pq($comment_user));
            $tmp = [];
            $tmp['name'] = trim(pq('div>div.comment__header>div>div>a')->text());
            $tmp['nickname'] = trim(pq('div>div.comment__header>div>div>span')->text());
            $tmp['message'] = trim(explode("Написано", pq('div>div.comment__body>div>div.comment__text')
                ->text())[0]);
            $tmp['pub_date'] = pq('div>div.comment__body>div>div>div.comment__date>a>time')->attr('datetime');
            array_push($comment_users, $tmp);
        }
        
        return $comment_users;
    }
    
    private static function get_solution_users($html) {
        phpQuery::newDocument($html);
        $ul_solution_users = pq('div.question__additionals>div.section_solutions>ul')->children('li');
        $solution_users = [];
        foreach ($ul_solution_users as $solution_user) {
            phpQuery::newDocument(pq($solution_user));
            $tmp = [];
            $tmp['name'] = trim(pq('div>article>header>div>div>a')->text());
            $tmp['nickname'] = trim(pq('div>article>header>div>div>span')->text());
            $tmp['description'] = trim(pq('div>article>header>div>div>div')->text());
            $tmp['message'] = trim(pq('div>article>div>div>div.answer__text')->text());
            $tmp['pub_date'] = pq('div>article>div>div>div.answer__date>a>time')->attr('datetime');
            $tmp['count_likes'] = trim(pq('div>footer>a.btn_like>span')->text());
            $tmp['count_comments'] = trim(pq('div>article>div>div>div.answer__comments-link>a>span>strong')->text());
            
            $ul_comment_users = pq('div>div.answer__comments>ul')->children('li');
            $comment_users = [];
            foreach ($ul_comment_users as $comment_user) {
                phpQuery::newDocument(pq($comment_user));
                $tmp2 = [];
                $tmp2['name'] = pq('div>div>div>div>a.user-summary__name')->text();
                $tmp2['nickname'] = trim(pq('div>div>div>div>span.user-summary__nickname')->text());
                $tmp2['message'] = trim(pq('div>div>div>div.comment__text')->text());
                $tmp2['pub_date'] = trim(pq('div>div>div>div.comment__date>a>time')->attr('datetime'));
                array_push($comment_users, $tmp2);
            }
            
            $tmp['comment_users'] = $comment_users;
            array_push($solution_users, $tmp);
        }
        
        return $solution_users;
    }
    
    private static function get_answer_users($html) {
        phpQuery::newDocument($html);
        $ul_answer_users = pq('div.question__additionals>div.section_answers>ul')->children('li');
        $answer_users = [];
        foreach ($ul_answer_users as $answer_user) {
            phpQuery::newDocument(pq($answer_user));
            $tmp['name'] = trim(pq('div>article>header>div>div>a')->text());
            $tmp['nickname'] = trim(pq('div>article>header>div>div>span')->text());
            $tmp['description'] = trim(pq('div>article>header>div>div>div')->text());
            $tmp['message'] = trim(pq('div>article>div>div>div.answer__text')->text());
            $tmp['pub_date'] = pq('div>article>div>div>div.answer__date>a>time')->attr('datetime');
            $tmp['count_likes'] = trim(pq('div>footer>a.btn_like>span')->text());
            $tmp['count_comments'] = trim(pq('div>footer>a.btn_comments-toggle>span>strong')->text());
            
            $ul_comment_users = pq('div>div.answer__comments>ul')->children('li');
            $comment_users = [];
            foreach ($ul_comment_users as $comment_user) {
                phpQuery::newDocument(pq($comment_user));
                $tmp2 = [];
                $tmp2['name'] = pq('div>div>div>div>a.user-summary__name')->text();
                $tmp2['nickname'] = trim(pq('div>div>div>div>span.user-summary__nickname')->text());
                $tmp2['message'] = trim(pq('div>div>div>div.comment__text')->text());
                $tmp2['pub_date'] = trim(pq('div>div>div>div.comment__date>a>time')->attr('datetime'));
                array_push($comment_users, $tmp2);
            }
            
            $tmp['comment_users'] = $comment_users;
            array_push($answer_users, $tmp);
        }
        
        return $answer_users;
    }
    
    private static function insert_user($data) {
        Yii::$app->db->createCommand()->insert('user', [
            'name' => $data['user_question_name'], 'nickname' => $data['user_question_nickname'],
            'description' => $data['user_question_description'], 'id_type_msg' => 1])->execute();
        
        foreach ($data['comment_users'] as $comment_user) {
            Yii::$app->db->createCommand()->insert('user', [
                'name' => $comment_user['name'], 'nickname' => $comment_user['nickname'],
                'description' => $comment_user['description'], 'id_type_msg' => 4])->execute();
        }
        
        foreach ($data['solution_users'] as $solution_user) {
            Yii::$app->db->createCommand()->insert('user', [
                'name' => $solution_user['name'], 'nickname' => $solution_user['nickname'],
                'description' => $solution_user['description'], 'id_type_msg' => 2])->execute();
            foreach ($solution_user['comment_users'] as $comment_user) {
                Yii::$app->db->createCommand()->insert('user', [
                    'name' => $comment_user['name'], 'nickname' => $comment_user['nickname'],
                    'description' => $comment_user['description'], 'id_type_msg' => 5])->execute();
            }
        }
        
        foreach ($data['answer_users'] as $answer_user) {
            Yii::$app->db->createCommand()->insert('user', [
                'name' => $answer_user['name'], 'nickname' => $answer_user['nickname'],
                'description' => $answer_user['description'], 'id_type_msg' => 3])->execute();
            foreach ($answer_user['comment_users'] as $comment_user) {
                Yii::$app->db->createCommand()->insert('user', [
                    'name' => $comment_user['name'], 'nickname' => $comment_user['nickname'],
                    'description' => $comment_user['description'], 'id_type_msg' => 6])->execute();
            }
        }
    }
    
    private static function insert_question($data) {
        $id_user = Yii::$app->db->createCommand("SELECT id FROM user WHERE id_type_msg=1 " 
            . "AND name=:name AND nickname=:nickname")->bindValues([':name' => $data['user_question_name'],
                ':nickname' => $data['user_question_nickname']])->queryOne()['id'];
        
        Yii::$app->db->createCommand()->insert('question', [
            'header' => $data['header_question'], 'message' => $data['msg_question'],
            'pub_date' => $data['pub_date_question'], 'count_views' => $data['count_views_question'],
            'count_subscribers' => $data['count_subscribers_question'], 'count_comments' => $data['count_comments_question'],
            'count_solutions' => $data['count_solutions_question'], 'count_answers' => $data['count_answers_question'],
            'id_user' => $id_user])->execute();
        
        return $id_user;
    }
    
    private static function insert_category_question($id_user, $data) {
        $id_question = Yii::$app->db->createCommand("SELECT id FROM question WHERE id_user=:id_user")
            ->bindValue(':id_user', $id_user)->queryOne()['id'];
        
        for ($i = 0; $i < count($data['categories']); $i++) {
            Yii::$app->db->createCommand()
                ->insert('category_question', ['id_question' => $id_question, 'name' => $data['categories'][$i]])->execute();
        }
        
        return $id_question;
    }
    
    private static function insert_solution($id_question, $data) {
        for ($i = 0; $i < count($data['solution_users']); $i++) {
            $id_user = Yii::$app->db->createCommand("SELECT id FROM user WHERE id_type_msg=2 " 
                . "AND name=:name AND nickname=:nickname")->bindValues([':name' => $data['solution_users'][$i]['name'],
                    ':nickname' => $data['solution_users'][$i]['nickname']])->queryOne()['id'];
            Yii::$app->db->createCommand()->insert('solution', ['id_user' => $id_user, 'id_question' => $id_question,
                'message' => $data['solution_users'][$i]['message'], 
                'pub_date' => $data['solution_users'][$i]['pub_date'],
                'count_likes' => $data['solution_users'][$i]['count_likes'], 
                'count_comments' => $data['solution_users'][$i]['count_comments']])->execute();
        }
    }
    
    private static function insert_answer($id_question, $data) {
        for ($i = 0; $i < count($data['answer_users']); $i++) {
            $id_user = Yii::$app->db->createCommand("SELECT id FROM user WHERE id_type_msg=3 " 
                . "AND name=:name AND nickname=:nickname")->bindValues([':name' => $data['answer_users'][$i]['name'],
                    ':nickname' => $data['answer_users'][$i]['nickname']])->queryOne()['id'];
            Yii::$app->db->createCommand()->insert('answer', ['id_user' => $id_user, 'id_question' => $id_question,
                'message' => $data['answer_users'][$i]['message'], 
                'pub_date' => $data['answer_users'][$i]['pub_date'],
                'count_likes' => $data['answer_users'][$i]['count_likes'], 
                'count_comments' => $data['answer_users'][$i]['count_comments']])->execute();
        }
    }
    
    private static function insert_comment($data) {
        for ($i = 0; $i < count($data['comment_users']); $i++) {
            $id_user = Yii::$app->db->createCommand("SELECT id FROM user WHERE id_type_msg=4 " 
                . "AND name=:name AND nickname=:nickname")->bindValues([':name' => $data['comment_users'][$i]['name'],
                    ':nickname' => $data['comment_users'][$i]['nickname']])->queryOne()['id'];
            Yii::$app->db->createCommand()->insert('comment', ['id_user' => $id_user, 'id_type_msg' => 4,
                'message' => $data['comment_users'][$i]['message'], 
                'pub_date' => $data['comment_users'][$i]['pub_date']])->execute();
        }
        
        for ($i = 0; $i < count($data['solution_users']); $i++) {
            for ($j = 0; $j < count($data['solution_users'][$i]['comment_users']); $j++) {
                $id_user = Yii::$app->db->createCommand("SELECT id FROM user WHERE id_type_msg=5 " 
                    . "AND name=:name AND nickname=:nickname")
                    ->bindValues([':name' => $data['solution_users'][$i]['comment_users'][$j]['name'],
                        ':nickname' => $data['solution_users'][$i]['comment_users'][$j]['nickname']])->queryOne()['id'];
                Yii::$app->db->createCommand()->insert('comment', ['id_user' => $id_user, 'id_type_msg' => 5,
                    'message' => $data['solution_users'][$i]['comment_users'][$j]['message'], 
                    'pub_date' => $data['solution_users'][$i]['comment_users'][$j]['pub_date']])->execute();
            }
        }
        
        for ($i = 0; $i < count($data['answer_users']); $i++) {
            for ($j = 0; $j < count($data['answer_users'][$i]['comment_users']); $j++) {
                $id_user = Yii::$app->db->createCommand("SELECT id FROM user WHERE id_type_msg=6 " 
                    . "AND name=:name AND nickname=:nickname")
                    ->bindValues([':name' => $data['answer_users'][$i]['comment_users'][$j]['name'],
                        ':nickname' => $data['answer_users'][$i]['comment_users'][$j]['nickname']])->queryOne()['id'];
                Yii::$app->db->createCommand()->insert('comment', ['id_user' => $id_user, 'id_type_msg' => 6,
                    'message' => $data['answer_users'][$i]['comment_users'][$j]['message'], 
                    'pub_date' => $data['answer_users'][$i]['comment_users'][$j]['pub_date']])->execute();
            }
        }
    }
    
    public function actionToster($id = '206698')
    {
        $id = (int) $id;
        $html = file_get_contents('https://toster.ru/q/' . $id);
        phpQuery::newDocument($html);
        
        $ul_categories = pq('div.question_full>nav>ul')->children('li');
        $categories = [];
        foreach ($ul_categories as $category) {
            array_push($categories, trim(pq($category)->text()));
        }
        
        $data = [
            'user_question_name' => pq('header.question-head>div>div>a')->text(),
            'user_question_nickname' => trim(pq('header.question-head>div>div>span')->text()),
            'user_question_description' => trim(pq('header.question-head>div>div>div')->text()),
            'categories' => $categories,
            'count_solutions_question' => pq('div.question__additionals>div.section_solutions>header>strong>span')->text(),
            'count_answers_question' => pq('div.question__additionals>div.section_answers>header>strong>span')->text(),
            'header_question' => trim(pq('div.question_full>h1.question__title')->text()),
            'msg_question' => trim(pq('div.question_full>div.question__body>div.question__text')->html()),
            'pub_date_question' => pq('div.question_full>div.question__body>ul>li:nth-child(1)>span>time')->attr('datetime'),
            'count_views_question' => explode(" ", trim(pq('div.question_full>div.question__body>ul>li:nth-child(2)>span')->text()))[0],
            'count_subscribers_question' => trim(pq('div.question_full>footer>a:nth-child(1)>span')->text()),
            'count_comments_question' => trim(pq('div.question_full>footer>a:nth-child(2)>span>strong')->text()),
            'comment_users' => self::get_comment_users($html),
            'solution_users' => self::get_solution_users($html),
            'answer_users' => self::get_answer_users($html)
        ];
        
        $toster = Yii::$app->db->createCommand('SELECT * FROM toster WHERE toster_id=:id')->bindValue(':id', $id)->queryOne();
        if (!$toster) {
            self::insert_user($data);
            $id_user = self::insert_question($data);
            $id_question = self::insert_category_question($id_user, $data);
            self::insert_solution($id_question, $data);
            self::insert_answer($id_question, $data);
            self::insert_comment($data);
            
            Yii::$app->db->createCommand()->insert('toster', ['toster_id' => $id])->execute();
        }
        
        return $this->render('toster', [
            'data' => $data
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        Yii::$app->response->redirect(Url::to(['site/toster']), 301);
        Yii::$app->end();
        //return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
