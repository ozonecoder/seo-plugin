<?php namespace Ozc\SEO\Components;

use App;
use RainLab\Pages\Classes\Router;
use URL;
use Request;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use Ozc\SEO\models\Settings;
use Cms\Classes\ComponentBase;

class SeoPage extends ComponentBase{
	public $page;
	public $seo_title;
	public $seo_description;
	public $seo_keywords;
	public $canonical_url;
	public $redirect_url;
	public $robot_index;
	public $robot_follow;
	public $hasBlog;

	public $ogTitle;
	public $ogUrl;
	public $ogDescription;
	public $ogSiteName;
	public $ogFbAppId;
	public $ogLocale;
	public $ogImage;

	public $settings;
	private $hasStaticPage;

	public function componentDetails(){
		return [
			'name'        => 'SEO Page',
			'description' => 'Inject SEO Fields of CMS or STATIC pages'
		];
	}

	public function defineProperties(){
		return [];
	}

	public function onRun(){


        $this->hasStaticPage = is_null($this->page->baseFileName);

        $this->page = $this->getCurrentPage();
        if(is_null($this->page)){
            return false;
        }

        $this->settings = Settings::instance();
        $this->hasBlog = $this->page->hasComponent("blogPost");

		if(!is_null($this->page) && !$this->hasBlog) {

            $seoVars = ($this->hasStaticPage) ? $this->getStaticSeoVars() : $this->getSeoVars();
            $this->seo_title = !empty($seoTitle) ?  $seoVars->seoTitle : $this->page->title;
            $this->seo_description = $seoVars->seoDescription;
            $this->seo_keywords = $seoVars->seoKeywords;
            $this->canonical_url = $seoVars->canonicalUrl;
            $this->redirect_url = $seoVars->redirectUrl;
            $this->robot_follow = $seoVars->robotFollow;
            $this->robot_index = $seoVars->robotIndex;

			if($this->settings->enable_og_tags){
                $ogVars = ($this->hasStaticPage) ? $this->getStaticOgVars() : $this->getOgVars();
                $this->ogTitle = $ogVars->ogTitle;
                $this->ogDescription = $ogVars->ogDescription;
                $this->ogUrl = $ogVars->ogUrl;
                $this->ogSiteName = $ogVars->ogSiteName;
                $this->ogImage = $ogVars->ogImage;
                $this->ogFbAppId = $ogVars->ogFbAppId;
            }

		}

		//prevent redirects in dev or stating
		if(App::environment() != 'production'){
			$this->redirect_url = null;
		}

	}

    /**
     * @return mixed
     */
    private function getCurrentPage() {
        $currentPage = null;
        $url = Request::path();
        $theme = Theme::getActiveTheme();
        $router = new Router($theme);
        $currentPage = ($this->hasStaticPage)
            ? $router->findByUrl($url) : Page::load($theme, $this->page->baseFileName);

        return $currentPage;
    }

    /**
     * @return object
     */
    private function getSeoVars() {

        $seoVars = new \stdClass();
        $seoVars->seoTitle = isset($this->page->meta_title) ? $this->page->meta_title : null;
        $seoVars->seoDescription = isset($this->page->meta_description) ? $this->page->meta_description : null;
        $seoVars->seoKeywords = isset($this->page->seo_keywords) ? $this->page->seo_keywords : null;
        $seoVars->canonicalUrl = isset($this->page->canonical_url) ? $this->page->canonical_url : null;
        $seoVars->redirectUrl = isset($this->page->redirect_url) ? $this->page->redirect_url : null;
        $seoVars->robotFollow = isset($this->page->robot_follow) ? $this->page->robot_follow : null;
        $seoVars->robotIndex = isset($this->page->robot_index) ? $this->page->robot_index : null;

        return $seoVars;
    }

    /**
     * @return object
     */
    private function getStaticSeoVars() {
        $viewBagProperties = $this->page->getViewBag()->getProperties();
        $seoVars = new \stdClass();
        $seoVars->seoTitle = isset($viewBagProperties['meta_title']) ? $viewBagProperties['meta_title'] : null;
        $seoVars->seoDescription = isset($viewBagProperties['meta_description']) ? $viewBagProperties['meta_description'] : null;
        $seoVars->seoKeywords = isset($viewBagProperties['seo_keywords']) ? $viewBagProperties['seo_keywords'] : null;
        $seoVars->canonicalUrl = isset($viewBagProperties['canonical_url']) ? $viewBagProperties['canonical_url'] : null;
        $seoVars->redirectUrl = isset($viewBagProperties['redirect_url']) ? $viewBagProperties['redirect_url'] : null;
        $seoVars->robotFollow = isset($viewBagProperties['robot_follow']) ? $viewBagProperties['robot_follow'] : null;
        $seoVars->robotIndex = isset($viewBagProperties['robot_index']) ? $viewBagProperties['robot_index'] : null;

        return $seoVars;
    }

    /**
     * @return object
     */
    private function getOgVars() {
        $ogVars = new \stdClass();
        $ogVars->ogTitle = empty($this->page->meta_title) ? $this->page->title : $this->page->meta_title;
        $ogVars->ogDescription = $this->page->meta_description;
        $ogVars->ogUrl = empty($this->page->canonical_url) ? Request::url() : $this->page->canonical_url;
        $ogVars->ogSiteName = $this->settings->og_sitename;
        $ogVars->ogImage = $this->page->image;
        $ogVars->ogFbAppId = $this->settings->og_fb_appid;

        return $ogVars;
    }

    /**
     * @return object
     */
    private function getStaticOgVars() {
        $ogVars = new \stdClass();
        $ogVars->ogTitle = empty($this->page->getViewBag()->property('seo_title'))
            ? $this->page->title : $this->page->getViewBag()->property('seo_title');
        $ogVars->ogDescription = $this->page->getViewBag()->property('meta_description');
        $ogVars->ogUrl = empty($this->page->getViewBag()->property('canonical_url'))
            ? Request::url() : $this->page->getViewBag()->property('canonical_url');
        $ogVars->ogSiteName = $this->settings->og_sitename;
        $ogVars->ogImage = $this->page->image;
        $ogVars->ogFbAppId = $this->settings->og_fb_appid;

        return $ogVars;
    }

}
