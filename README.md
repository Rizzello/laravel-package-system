# Laravel Package System

## Installation

Private PHP packages can be installed using Composer. We need a little extra configuration in the composer.json file and
then we setup our SSH key. Follow along with these 3 easy steps, and don't miss the caveat at the end.

### 1. Point to the Git repository

Update composer.json and add the repository:

```
"repositories":[
    {
        "type": "vcs",
        "url": "git@github.com:Rizzello/laravel-package-system.git"
    }
]
```

### 2. Create an SSH key

Create an SSH Key on the machine on which you want to install the package.

If you are working on a development machine, you probably want to add the SSH key to your GitHub/BitBucket/GitLab
account. This gives access to all private repositories that your account has access to.

-   [Add an SSH key to a GitHub account](https://help.github.com/articles/adding-a-new-ssh-key-to-your-github-account/)
-   [Add an SSH key to a BitBucket account](https://confluence.atlassian.com/bitbucket/set-up-an-ssh-key-728138079.html#SetupanSSHkey-#installpublickeyStep3.AddthepublickeytoyourBitbucketsettings)
-   [Add an SSH key to a GitLab account](https://docs.gitlab.com/ee/gitlab-basics/create-your-ssh-keys.html)

In case you are configuring a deployment server, it would be better to configure an access key or deploy key. An access
key only provides access to a single repository and thus allows for more specific access management.

-   [Add a deploy key to a GitHub repository](https://developer.github.com/v3/guides/managing-deploy-keys/#deploy-keys)
-   [Add an access key to a BitBucket repository](https://confluence.atlassian.com/bitbucket/use-deployment-keys-294486051.html)
-   [Add a deploy key to a GitLab repository](https://docs.gitlab.com/ee/ssh/#deploy-keys)

### 3. Run composer

Now just composer require or composer install the package as usual.

```
composer require  rizzello/laravel-package-system
composer install
```
