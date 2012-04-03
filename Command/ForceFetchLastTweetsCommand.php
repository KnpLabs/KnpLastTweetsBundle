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
use Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\FetcherCacheableInterface;

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
                new InputArgument('usernames', InputArgument::REQUIRED, 'Twitter usernames'),
                new InputOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Max number of tweets', 10),
            ))
            ->setDescription('Fetch the last tweets a bundle, use comma delimiter for few usernames.')
            ->setHelp(<<<EOT
The <info>knp-last-tweets:force-fetch</info> command fetches the last tweets of a users.

It is useful to force the caching via a cron job rather than letting a visitor request do it.

If you need to pass more than 1 usernames, just use comma delimiter.

<info>php app/console knp-last-tweets:force-fetch knplabs</info>
<info>php app/console knp-last-tweets:force-fetch knplabs,knplabsru</info>
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
        
        if (!$twitter instanceof FetcherCacheableInterface) {
            $output->writeln(
                '<error>You\'re using the twitter fetcher driver "'.get_class($twitter)."\"\n".
                "This command only works if the driver is cacheable.\n".
                'Use zend_cache for example.</error>');
            return;
        }

        $usernames = explode(',', $input->getArgument('usernames'));
        $limit = $input->getOption('limit');
        
        $output->writeln('Fetching the <info>'.$limit.'</info> last tweets of <info>' . implode(', ', $usernames) . '</info>');

        try {
            $tweets = $twitter->forceFetch($usernames, $limit, true);
        } catch (TwitterException $e) {
            $output->writeln('<error>Unable to fetch last tweets: '.$e->getMessage().'</error>');
        }
    }

}
