<?php

namespace DcodeGroup\XeroIntegration\Commands;

use Illuminate\Console\Concerns\ConfiguresPrompts;
use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class MakeXeroData extends GeneratorCommand implements PromptsForMissingInput
{
    use ConfiguresPrompts;
    use CreatesMatchingTest;

    protected $signature = 'make:xero-data
        {name : The name of the Xero Data class}
        {xero-data : The name of the Xero Data to extend}
        {--model= : What model would you like to use for this Data class?}
        {--force : Overwrite the class if it already exists}';

    protected $description = 'Create a Xero Data class for a given model';

    protected $type = 'XeroData';

    protected function promptForMissingArgumentsUsing(): array
    {
        $dataClasses = $this->getClassesInDirectory(dirname(__DIR__).'/../Data');

        return [
            'name' => fn () => text(
                label: 'What is the name of the Data class to extend a XeroData class?',
                placeholder: 'e.g. InvoiceData',
                required: 'The class name is required',
                validate: ['name' => ['required', 'string', 'min:3', 'max:255', 'alphanum']],
            ),
            'xero-data' => fn () => select(
                label: 'What is the name of the Xero Data to extend?',
                required: 'The Xero Data class name is required',
                options: $dataClasses,
                validate: ['xero-data' => ['required', 'string', 'in:'.implode(',', $dataClasses)]],
                info: 'Select the Xero Data class to extend from the list of available classes.',
            ),
        ];
    }

    /**
     * Summary of afterPromptingForMissingArguments
     */
    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output): void
    {
        $modelClasses = $this->findAvailableModels();

        $input->setOption('model', select(
            label: 'What is the name of the model to use for this Data class?',
            options: $modelClasses,
            validate: ['model' => ['string', 'in:'.implode(',', $modelClasses)]],
            info: 'Select the model class to use for this Data class from the list of available classes.',
            default: $this->option('model'),
        ));
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
            ->replaceClass($stub, $name);
    }

    protected function replaceExtends(&$stub)
    {
        $xeroData = $this->argument('xero-data');

        str_replace(
            '{{ extends }}',
            $xeroData,
            $stub
        );

        return $this;
    }

    protected function replaceModel(&$stub)
    {
        $model = $this->option('model');

        if (empty($model)) {
            $model = 'Model';
        }

        str_replace(
            '{{ model }}',
            $model,
            $stub
        );

        return $this;
    }

    /**
     * Summary of getClassesInDirectory
     */
    protected function getClassesInDirectory(string $directory): array
    {
        return collect(File::allFiles($directory))
            ->map(fn ($file) => class_basename($file->getFilenameWithoutExtension()))
            ->toArray();
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
