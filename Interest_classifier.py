# credentials to consume Twitter API
TWITTER_CONSUMER_KEY = 'qJSNJwu5V1Q21QTDYY2IZ3UGA'
TWITTER_CONSUMER_SECRET = 'hhCgYgLaRWSHXcCdA09N3qIUk3JKFXWAxVPQS7ES9NMjT4gu3o'
TWITTER_ACCESS_TOKEN_KEY = '227584299-k53yk9HxwG2jwwNJMf4lfs20RXc4OhoBApobgZsE'
TWITTER_ACCESS_TOKEN_SECRET = 'pvA9JkE4rv4C55zY3SOX8qZP14vriOFghsUrMvRcazL68'

# twitter user that we will be profiling using our news classifier.
TWITTER_USER = 'katyperry'

# MONKEYLEARN SETTINGS
MONKEYLEARN_TOKEN = '81137672d671ea08af4938ad6a970f4f7bdc8a21'

MONKEYLEARN_CLASSIFIER_BASE_URL = 'https://api.monkeylearn.com/api/v1/categorizer/'
MONKEYLEARN_EXTRACTOR_BASE_URL = 'https://api.monkeylearn.com/api/v1/extraction/'

# classifier to detect the tweet's language
MONKEYLEARN_LANG_CLASSIFIER_ID = 'cl_hDDngsX8'

# classifier to detect the tweet's topics
MONKEYLEARN_TOPIC_CLASSIFIER_ID = 'cl_5icAVzKR'

# extractor to extract keywords from tweets
MONKEYLEARN_EXTRACTOR_ID = 'ex_y7BPYzNG'


# tweepy the Twitter API from Python
import tweepy
import re

# Authenticate to Twitter API
auth = tweepy.OAuthHandler(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET)
auth.set_access_token(TWITTER_ACCESS_TOKEN_KEY, TWITTER_ACCESS_TOKEN_SECRET)
api = tweepy.API(auth)

from random import shuffle


def get_friends_descriptions(api, twitter_account, max_users=100):
    """
    Return the bios of the people that a user follows

    api -- the tweetpy API object
    twitter_account -- the Twitter handle of the user
    max_users -- the maximum amount of users to return
    """

    user_ids = api.friends_ids(twitter_account)
    shuffle(user_ids)

    following = []
    for start in xrange(0, min(max_users, len(user_ids)), 100):
        end = start + 100
        following.extend(api.lookup_users(user_ids[start:end]))

    descriptions = []
    for user in following:
        description = re.sub(r'(https?://\S+)', '', user.description)

        # Only descriptions with at least ten words.
        if len(re.split(r'[^0-9A-Za-z]+', description)) > 10:
            descriptions.append(description.strip('#').strip('@'))

    return descriptions

# Get the descriptions of the people that twitter_user is following.
descriptions = get_friends_descriptions(api, TWITTER_USER, max_users=300)


def get_tweets(api, twitter_user, tweet_type='timeline', max_tweets=200, min_words=5):
    tweets = []

    full_tweets = []
    step = 200  # Maximum value is 200.
    for start in xrange(0, max_tweets, step):
        end = start + step

        # Maximum of `step` tweets, or the remaining to reach max_tweets.
        count = min(step, max_tweets - start)

        kwargs = {'count': count}
        if full_tweets:
            last_id = full_tweets[-1].id
            kwargs['max_id'] = last_id - 1

        if tweet_type == 'timeline':
            current = api.user_timeline(twitter_user, **kwargs)
        else:
            current = api.favorites(twitter_user, **kwargs)

        full_tweets.extend(current)

    for tweet in full_tweets:
        text = re.sub(r'(https?://\S+)', '', tweet.text)

        score = tweet.favorite_count + tweet.retweet_count
        if tweet.in_reply_to_status_id_str:
            score -= 15

        # Only tweets with at least five words.
        if len(re.split(r'[^0-9A-Za-z]+', text)) > min_words:
            tweets.append((text, score))

    return tweets


tweets = []
tweets.extend(get_tweets(api, TWITTER_USER, 'timeline', 1000))  # 400 = 2 requests (out of 15 in the window).
tweets.extend(get_tweets(api, TWITTER_USER, 'favorites', 400))  # 1000 = 5 requests (out of 180 in the window).

tweets = map(lambda t: t[0], sorted(tweets, key=lambda t: t[1], reverse=True))[:500]


### Detect language with MonkeyLearn API

# We'll keep only English tweets and bios

import requests
import json

# This is a handy function to classify a list of texts in batch mode (much faster)
def classify_batch(text_list, classifier_id):
    """
    Batch classify texts
    text_list -- list of texts to be classified
    classifier_id -- id of the MonkeyLearn classifier to be applied to the texts
    """
    results = []

    step = 250
    for start in xrange(0, len(text_list), step):
        end = start + step

        data = {'text_list': text_list[start:end]}

        response = requests.post(
            MONKEYLEARN_CLASSIFIER_BASE_URL + classifier_id + '/classify_batch_text/',
            data=json.dumps(data),
            headers={
                'Authorization': 'Token {}'.format(MONKEYLEARN_TOKEN),
                'Content-Type': 'application/json'
            })

        try:
            results.extend(response.json()['result'])
        except:
            print response.text
            raise

    return results


def filter_language(texts, language='English'):
    # Get the language of the tweets and bios using Monkeylearn's Language classifier
    lang_classifications = classify_batch(texts, MONKEYLEARN_LANG_CLASSIFIER_ID)

    # Only keep the descriptions that are writtern in English.
    lang_texts = [
        text
        for text, prediction in zip(texts, lang_classifications)
        if prediction[0]['label'] == language
        ]

    return lang_texts


descriptions_english = filter_language(descriptions)
print "Descriptions found: {}".format(len(descriptions_english))

tweets_english = filter_language(tweets)
print "Tweets found: {}".format(len(tweets_english))


### Expand context of the data

# The following section is optional. You can use Bing search to expand the context of the data to obtain better classification accuracy.
#

def extract_keywords(text_list, max_keywords):
    results = []
    step = 250
    for start in xrange(0, len(text_list), step):
        end = start + step

        data = {'text_list': text_list[start:end],
                'max_keywords': max_keywords}

        response = requests.post(
            MONKEYLEARN_EXTRACTOR_BASE_URL + MONKEYLEARN_EXTRACTOR_ID + '/extract_batch_text/',
            data=json.dumps(data),
            headers={
                'Authorization': 'Token {}'.format(MONKEYLEARN_TOKEN),
                'Content-Type': 'application/json'
            })

        try:
            results.extend(response.json()['result'])
        except:
            print response.text
            raise

    return results


import multiprocessing.dummy as multiprocessing

BING_KEY = ''
EXPAND_TWEETS = False


def _bing_search(query):
    MAX_EXPANSIONS = 5

    params = {
        'Query': u"'{}'".format(query),
        '$format': 'json',
    }

    response = requests.get(
        'https://api.datamarket.azure.com/Bing/Search/v1/Web',
        params=params,
        auth=(BING_KEY, BING_KEY)
    )

    try:
        response = response.json()
    except ValueError as e:
        print e
        print response.status_code
        print response.text
        texts = []
        return
    else:
        texts = []
        for result in response['d']['results'][:MAX_EXPANSIONS]:
            texts.append(result['Title'])
            texts.append(result['Description'])

    return u" ".join(texts)


def _expand_text(text):
    result = text + u"\n" + _bing_search(text)
    print result
    return result


def expand_texts(texts):
    # First extract hashtags and keywords from the text to form the queries
    queries = []
    keyword_list = extract_keywords(texts, 10)
    for text, keywords in zip(texts, keyword_list):
        query = ' '.join([item['keyword'] for item in keywords])
        query = query.lower()
        tags = re.findall(r"#(\w+)", text)
        for tag in tags:
            tag = tag.lower()
            if tag not in query:
                query = tag + ' ' + query
        queries.append(query)

    pool = multiprocessing.Pool(2)
    return pool.map(_expand_text, queries)

# Use Bing search to expand the context of descriptions
expanded_descriptions = descriptions_english
# expanded_descriptions = expand_texts(descriptions_english)


# Use Bing search to expand the context of tweets
if EXPAND_TWEETS:
    expanded_tweets = expand_texts(tweets_english)
else:
    expanded_tweets = tweets_english


### Detect the topics with MonkeyLearn API

from collections import Counter


def category_histogram(texts, short_texts):
    # Classify the bios and tweets with MonkeyLearn's news classifier.
    topics = classify_batch(texts, MONKEYLEARN_TOPIC_CLASSIFIER_ID)

    # The histogram will keep the counters of how many texts fall in
    # a given category.
    histogram = Counter()
    samples = {}

    for classification, text, short_text in zip(topics, texts, short_texts):

        # Join the parent and child category names in one string.
        category = classification[0]['label']
        probability = classification[0]['probability']

        if len(classification) > 1:
            category += '/' + classification[1]['label']
            probability *= classification[1]['probability']

        MIN_PROB = 0.0
        # Discard texts with a predicted topic with probability lower than a treshold
        if probability < MIN_PROB:
            continue

        # Increment the category counter.
        histogram[category] += 1

        # Store the texts by category
        samples.setdefault(category, []).append((short_text, text))

    return histogram, samples

# Classify the expanded bios of the followed users using MonkeyLearn, return the historgram
descriptions_histogram, descriptions_categorized = category_histogram(expanded_descriptions, descriptions_english)

# Print the catogories sorted by most frequent
for topic, count in descriptions_histogram.most_common():
    print count, topic


# Classify the expanded tweets using MonkeyLearn, return the historgram
tweets_histogram, tweets_categorized = category_histogram(expanded_tweets, tweets_english)

# Print the catogories sorted by most frequent
for topic, count in tweets_histogram.most_common():
    print count, topic


### Plot the most popular topics

get_ipython().magic(u'matplotlib inline')
import matplotlib.pyplot as plt

# Add the two histograms (bios and tweets) to a total histogram
total_histogram = tweets_histogram + descriptions_histogram

# Get the top N categories by frequency
max_categories = 6
top_categories, values = zip(*total_histogram.most_common(max_categories))

# Plot the distribution of the top categories with a pie chart
plt.figure(1, figsize=(5, 5))
ax = plt.axes([0.1, 0.1, 0.8, 0.8])

plt.pie(
    values,
    labels=top_categories,
    shadow=True,
    colors=[
        (0.86, 0.37, 0.34), (0.86, 0.76, 0.34), (0.57, 0.86, 0.34), (0.34, 0.86, 0.50),
        (0.34, 0.83, 0.86), (0.34, 0.44, 0.86), (0.63, 0.34, 0.86), (0.86, 0.34, 0.70),
    ],
    radius=20,
    autopct='%1.f%%',
)

plt.axis('equal')
plt.show()


### Get the keywords of each category with MonkeyLearn API

joined_texts = {}

for category in tweets_categorized:
    if category not in top_categories:
        continue

    expanded = 0
    joined_texts[category] = u' '.join(map(lambda t: t[expanded], tweets_categorized[category]))

keywords = dict(zip(joined_texts.keys(), extract_keywords(joined_texts.values(), 20)))

for cat, kw in keywords.iteritems():
    top_relevant = map(
        lambda x: x.get('keyword'),
        sorted(kw, key=lambda x: float(x.get('relevance')), reverse=True)
    )

    print u"{}: {}".format(cat, u", ".join(top_relevant))

from IPython.display import Javascript

libs = [
    "http://d3js.org/d3.v3.min.js",
    "http://www.jasondavies.com/wordcloud/d3.layout.cloud.js"
]


def plot_wordcloud(wordcloud):
    return Javascript("""
                var fill = d3.scale.category20b();

                var cloudNode = $('<div id="wordcloud"></div>');
                element.append(cloudNode);

                var wordData = JSON.parse('%s');
                console.log(wordData);

                function draw(words) {
                    d3.select("#wordcloud").append("svg")
                        .attr("width", 600)
                        .attr("height", 502)
                        .append("g")
                        .attr("transform", "translate(300,160)")
                        .selectAll("text")
                        .data(words)
                        .enter().append("text")
                        .style("font-size", function (d) { return d.size + "px"; })
                        .style("font-family", "impact")
                        .style("fill", function (d, i) { return fill(i); })
                        .attr("text-anchor", "middle")
                        .attr("transform", function (d) {
                            return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
                        })
                        .text(function (d) { return d.text; });
                }
                console.log($("#wordcloud"));

                d3.layout.cloud().size([600, 502])
                    .timeInterval(10)
                    .words(wordData)
                    .padding(1)
                    .rotate(function () { return 0; })
                    .font('impact')
                    .fontSize(function (d) { return d.size; })
                    .on("end", draw)
                    .start();

        """ % json.dumps(wordcloud), lib=libs)


wordcloud = map(
    lambda s: {'text': s['keyword'], 'size': 15 + 40 * float(s['relevance'])},
    keywords['Society/Special Occasions']
)
plot_wordcloud(wordcloud)
