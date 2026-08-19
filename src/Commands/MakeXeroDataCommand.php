<?php

namespace Dcodegroup\XeroIntegration\Commands;

use Illuminate\Console\Concerns\ConfiguresPrompts;
use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\suggest;
use function Laravel\Prompts\text;

class MakeXeroDataCommand extends GeneratorCommand implements PromptsForMissingInput
{
    use ConfiguresPrompts;
    use CreatesMatchingTest;

    protected $signature = 'make:xero-data
        {name : The name of the Xero Data class}
        {xero-data : The name of the Xero Data to extend}
        {model : The name of the Model}
        {--force : Overwrite the class if it already exists}';

    protected $description = 'Create a Xero Data class for a given model';

    protected $type = 'XeroData';

    protected function promptForMissingArgumentsUsing(): array
    {
        $dataClasses = $this->getClassesInDirectory(__DIR__.'/../Data', 'Data');
        $models = $this->getClassesInDirectory(app_path('Models'));

        return [
            'name' => fn () => text(
                label: 'What is the name of the Data class to extend a XeroData class?',
                placeholder: 'e.g. XeroInvoiceData',
                validate: ['name' => ['sometimes', 'string', 'min:3', 'max:255', 'regex:/^[a-zA-Z0-0\\\]+$/']],
            ),
            'xero-data' => fn () => suggest(
                label: 'What is the name of the Xero Data to extend?',
                options: fn (string $value) => $dataClasses->filter(fn (string $class) => Str::contains($class, $value, ignoreCase: true))->values()->all(),
                validate: ['xero-data' => ['sometimes', 'string']],
            ),
            'model' => fn () => suggest(
                label: 'What is the name of the Model?',
                options: fn (string $value) => $models->filter(fn (string $class) => Str::contains($class, $value, ignoreCase: true))->values()->all(),
                validate: ['model' => ['sometimes', 'string']],
            ),
        ];
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)
            ->replaceExtends($stub)
            ->replaceModel($stub)
            ->replaceClass($stub, $name);
    }

    /**
     * Summary of replaceExtends
     */
    protected function replaceExtends(string &$stub): self
    {
        $xeroData = $this->argument('xero-data');

        if (empty($xeroData)) {
            $xeroData = $this->argument('name');
        }

        $stub = str_replace(
            '{{ xero-data }}',
            $xeroData,
            $stub
        );

        return $this;
    }

    /**
     * Summary of replaceModel
     *
     * @para string $stub
     */
    protected function replaceModel(string &$stub): self
    {
        $model = $this->argument('model');

        if (empty($model)) {
            $model = 'Model';
        }

        $stub = str_replace(
            '{{ model }}',
            $model,
            $stub
        );

        $stub = str_replace(
            '{{ model_var }}',
            strtolower($model),
            $stub
        );

        return $this;
    }

    /**
     * Summary of getClassesInDirectory
     *
     * @return Collection<string>
     */
    protected function getClassesInDirectory(string $directory, ?string $suffix = null): Collection
    {
        return collect(File::allFiles($directory))
            ->reduce(function (Collection $files, $file) use ($suffix) {
                $name = class_basename($file->getFilenameWithoutExtension());

                if (! empty($suffix) && ! str_ends_with($name, $suffix)) {
                    return $files;
                }

                if (Str::startsWith($name, 'Abstract')) {
                    return $files;
                }

                $files->push($name);

                return $files;
            }, collect());
    }

    /**
     * Summary of getStub
     */
    protected function getStub(): string
    {
        return __DIR__.'/../Stubs/xero-data.stub';
    }

    /**
     * Summary of getDefaultNamespace
     *
     * @param  mixed  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Data\Xero';
    }
}
