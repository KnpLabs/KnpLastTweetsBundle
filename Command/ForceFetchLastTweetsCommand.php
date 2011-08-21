<?php

namespace Knp\Bundle\LastTweetsBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException;
use Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\LastTweetsFetcherCacheableInterface;

/**
 * Fetch last tweets and cache them.
 *
 */
class ForceFetchLastTweetsCommand extends ContainerAwareCommand
{
    private $generator;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'Twitter username'),
                new InputOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Max number of tweets', 10),
            ))
            ->setDescription('Fetch the last tweets a bundle')
            ->setHelp(<<<EOT
The <info>generate:bundle</info> command helps you generates new bundles.

By default, the command interacts with the developer to tweak the generation.
Any passed option will be used as a default value for the interaction
(<comment>--namespace</comment> is the only one needed if you follow the
conventions):

<info>php app/console generate:bundle --namespace=Acme/BlogBundle</info>

Note that you can use <comment>/</comment> instead of <comment>\\</comment> for the namespace delimiter to avoid any
problem.

If you want to disable any user interaction, use <comment>--no-interaction</comment> but don't forget to pass all needed options:

<info>php app/console generate:bundle --namespace=Acme/BlogBundle --dir=src [--bundle-name=...] --no-interaction</info>

Note that the bundle namespace must end with "Bundle".
EOT
            )
            ->setName('knp-last-tweets:force-fetch')
        ;
    }

    /**
     * @see Command
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $twitter = $this->getContainer()->get('knp_last_tweets.last_tweets_fetcher');
        
        if (!$twitter instanceof LastTweetsFetcherCacheableInterface) {
            $output->writeln(
                '<error>You\'re using the twitter fetcher driver "'.get_class($twitter)."\"\n".
                "This command only works if the driver is cacheable.\n".
                'Use zend_cache for example.</error>');
            return;
        }

        $username = $input->getArgument('username');
        $limit = $input->getOption('limit');
        
        $output->writeln('Fetching the <info>'.$limit.'</info> last tweets of <info>'.$username.'</info>');

        try {
            $tweets = $twitter->forceFetch($username, $limit, true);
        } catch (TwitterException $e) {
            $output->writeln('<error>Unable to fetch last tweets: '.$e->getMessage().'</error>');
        }
    }

}
