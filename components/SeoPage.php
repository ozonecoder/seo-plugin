<?php namespace Ozc\SEO\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use Request;
use Ozc\SEO\models\Settings;
use URL;
use App;

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

	public function componentDetails(){
		return [
			'name'        => 'SEO Page',
			'description' => 'Inject SEO Fields of CMS pages'
		];
	}

	public function defineProperties(){
		return [];
	}

	public function onRun(){

        if(is_null($this->page->baseFileName)){
            return false;
        }

		$theme = Theme::getActiveTheme();
		$page = Page::load($theme,$this->page->baseFileName);
		$this->page["hasBlog"] = false;

		$this->settings = $this->page["settings"] = Settings::instance();


		if(!is_null($page) && !$page->hasComponent("blogPost")){
			$this->seo_title = $this->page["seo_title"] = empty($this->page->meta_title) ? $this->page->title : $this->page->meta_title;
			$this->seo_description = $this->page["seo_description"] = $this->page->meta_description;
			$this->seo_keywords = $this->page["seo_keywords"] = $this->page->seo_keywords;
			$this->canonical_url = $this->page["canonical_url"] = $this->page->canonical_url;
			$this->redirect_url = $this->page["redirect_url"] = $this->page->redirect_url;
			$this->robot_follow = $this->page["robot_follow"] = $this->page->robot_follow;
			$this->robot_index = $this->page["robot_index"] = $this->page->robot_index;

			$settings = Settings::instance();

			if($settings->enable_og_tags){
				$this->ogTitle = empty($this->page->meta_title) ? $this->page->title : $this->page->meta_title;
				$this->ogDescription = $this->page->meta_description;
				$this->ogUrl = empty($this->page->canonical_url) ? Request::url() : $this->page->canonical_url ;
				$this->ogSiteName = $settings->og_sitename;
				$this->ogImage = $this->page->image;
				$this->ogFbAppId = $settings->og_fb_appid;
			}

		}else{
			$this->hasBlog = $this->page["hasBlog"] = true;
		}

		//prevent redirects in dev or stating
		if(App::environment() != 'production'){
			$this->redirect_url = null;
		}


	}

}
