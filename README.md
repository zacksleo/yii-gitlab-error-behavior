# yii-gitlab-error-behavior
gitlab error behavior for yii1



[![Latest Stable Version](https://poser.pugx.org/zacksleo/yii-gitlab-error-behavior/version)](https://packagist.org/packages/zacksleo/yii-gitlab-error-behavior)
[![Total Downloads](https://poser.pugx.org/zacksleo/yii-gitlab-error-behavior/downloads)](https://packagist.org/packages/zacksleo/yii-gitlab-error-behavior)
[![License](https://poser.pugx.org/zacksleo/yii-gitlab-error-behavior/license)](https://packagist.org/packages/zacksleo/yii-gitlab-error-behavior)


## Usage


### set component

```
    'errorHandler' => [
        'errorAction' => 'site/error',
    ],

```
### set behavior in SiteController

set apiRoot, privateToken and projectName

```
    public function behaviors()
    {
        return [
            'behaviors' => [
                'class' => ErrorBehavior::className(),
                'apiRoot' => 'http://gitlab.com/api/v3/',
                'privateToken' => 'privateToken',
                'projectName' => 'demo/project'
            ]
        ];
    }
    
```

## Screenshoot

![](http://ww1.sinaimg.cn/large/675eb504gy1fe0mhspoo6j212706vabc.jpg)