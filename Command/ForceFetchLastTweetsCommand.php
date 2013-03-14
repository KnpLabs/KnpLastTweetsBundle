<?php

namespace Knp\Bundle\LastTweetsBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Knp\Bundle\LastTweetsBundle\Twitter\Exception\TwitterException;
use Knp\Bundle\LastTweetsBundle\Twitter\LastTweetsFetcher\FetcherInterface;

/**
 * Fetch last tweets and cache them.
 */
class ForceFetchLastTweetsCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Twitter usernames'),
                new InputOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Max number of tweets', 10),
            ))
            ->setDescription('Fetch the last tweets a bundle.')
            ->setHelp(<<<EOT
The <info>knp-last-tweets:force-fetch</info> command fetches the last tweets of a users.

It is useful to force the caching via a cron job rather than letting a visitor request do it.

<info>php app/console knp-last-tweets:force-fetch knplabs</info>
<info>php app/console knp-last-tweets:force-fetch knplabs knplabsru knpuniversity</info>
EOT
            )
            ->setName('knp-last-tweets:force-fetch')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var FetcherInterface $twitter */
        $twitter = $this->getContainer()->get('knp_last_tweets.last_tweets_fetcher');

        if ($twitter->hasCache()) {
            $output->writeln(
                "<error>You're using the twitter fetcher without cache\n".
                'This command only works if the driver has cache set.</error>'
            );

            return 1;
        }

        $limit     = $input->getOption('limit');
        $usernames = $input->getArgument('username');

        $output->writeln('Fetching the <info>'.$limit.'</info> last tweets of <info>' . implode(', ', $usernames) . '</info>');

        try {
            $twitter->fetch($usernames, $limit, true);
        } catch (TwitterException $e) {
            $output->writeln('<error>Unable to fetch last tweets: '.$e->getMessage().'</error>');

            return 1;
        }

        return 0;
    }
}
