<?php

namespace App\Models;

use App\Actions\SourceActions\GetGithubAppRepositories;
use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class Repository extends Model
{
    use Sushi;

    protected static $rows; // Declare the static property for Sushi caching
    protected static $source;

    protected function sushiShouldCache()
    {
        return true;
    }

    public static function setSource(Source $source)
    {
        self::$source = $source;
        self::$rows = null; // Clear cached rows
    }

    public function getRows()
    {
        if (!self::$source) {
            throw new \Exception('Source is not set.');
        }

        $repositories = (new GetGithubAppRepositories(self::$source))->execute();

        return collect($repositories)->map(function ($repository) {
            return [
                'id' => $repository['id'],
                'name' => $repository['name'],
                'owner' => $repository['owner']['login'],
                'keys_url' => $repository['keys_url'],
                'branches_url' => $repository['branches_url'],
                'ssh_url' => $repository['ssh_url'],
                'default_branch' => $repository['default_branch'],
                'source_id' => self::$source->id,
            ];
        })->toArray();
    }

    //source
    public function source()
    {
        return $this->belongsTo(Source::class);
    }
}
