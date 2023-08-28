<?php

namespace framework\Console\command;


use framework\Console\input\Command;
use framework\Console\Output;
use framework\Console\Input;
use framework\Console\output\table\Table;
use framework\Exception\InvalidArgumentException;
use framework\Console\output\ProgressBar\ProgressBar;
use framework\Http\Download as HttpDownload;

class Download extends Command
{
    public function configure()
    {
        // 指令配置
        $this->setName('download')
            ->setDescription('download file for a command');

        $this->addUsage('[arguments ...]');

        $this->addArgument('file', function ($argument) {
            $argument->setDescription('The name of file url');
            $argument->setRequired();
        });
    }

    public function execute(Input $input, Output $output)
    {

        $file = $input->getArgument('file');


        //设置分段下载的字节大小
        $burst = 4048000;
        //设置保存到服务器本地的文件名
        $filename = storage_path(basename($file));

        try {

            //初始化下载器
            $download = new HttpDownload();
            $download->setUrl($file)
                ->setBurst($burst)
                ->start();

            $total = $download->getFileSize();

            $this->progressBar = $output->ProgressBar($total, function ($bar) {

                $bar->start();
                $bar->finish(function ($res) {
                    return sprintf("开始时间: %s , 完成时间：%s, 用时： %s 秒 ", $res['starttime'], $res['endtime'], $res['time']);
                });

                $bar->setStyle(function ($style) {
                    $style->setForeground('green')
                        ->setBackground('default');
                });

                return $bar;

            });


            //开始下载
            $download->saveFile($filename, function ($filesize) {

                $this->progressBar->display(function ($bar) use ($filesize) {
                    $bar->setCurrentBarText(sprintf("%sk", ceil($filesize / 1024)));
                    $bar->updateProcessBar($filesize);
                });
            });

        } catch (Exception $exception) {
            var_dump($exception->getMessage());
        }

    }
}
