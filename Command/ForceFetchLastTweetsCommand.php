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
The <info>knp-last-tweets:force-fetch</info> command fetches the last tweets of a user.

It is useful to force the caching via a cron job rather than letting a visitor request do it.

<info>php app/console -last-tweets:force-fetch knplabs</info>
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
