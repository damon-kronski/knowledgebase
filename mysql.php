
CREATE TABLE kb_entries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  author INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  tags VARCHAR(512),
  content VARCHAR(2500),
  createdon TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  changedon TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE kb_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL UNIQUE,
  fullname VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  sessionkey VARCHAR(512),
  sessionset TIMESTAMP,
  active bit,
  role INT NOT NULL,
  createdon TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  changedon TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO kb_users (id, username,fullname,password,active,role) VALUES (1, 'admin','Administrator','9c217906529ba15fe332c47985c7fc69ad0e9e1e95b92ced595add995460b470d669b091e0695a655bb469107c64b1f19066b54cc571556e2cbb46a50c0d15a8',1,1);


INSERT INTO kb_entries (author,title,tags,content) VALUES(1,'Test Page','Test','Hello World!');
INSERT INTO kb_entries (author,title,tags,content) VALUES(1,'Testing Stuff','Stuff,Things','Also just some Test.
Not really special yet.');

INSERT INTO kb_entries (id,author,title,tags,content) VALUES(1,1,'More Stuff','Test,Stuff','[arbitrary case-insensitive reference text]: https://www.mozilla.org
[1]: http://slashdot.org
[link text itself]: http://www.reddit.com

# H1
## H2
### H3
#### H4

Emphasis, aka italics, with *asterisks* or _underscores_.
Strong emphasis, aka bold, with **asterisks** or __underscores__.
Combined emphasis with **asterisks and _underscores_**.
Strikethrough uses two tildes. ~~Scratch this.~~

1. First ordered list item
2. Another item
⋅⋅* Unordered sub-list.
1. Actual numbers don\'t matter, just that it\'s a number
⋅⋅1. Ordered sub-list
4. And another item.

* Unordered list can use asterisks
- Or minuses
+ Or pluses

[I\'m an inline-style link](https://www.google.com)

[I\'m an inline-style link with title](https://www.google.com "Google\'s Homepage")

[I\'m a reference-style link][Arbitrary case-insensitive reference text]

[I\'m a relative reference to a repository file](../blob/master/LICENSE)

[You can use numbers for reference-style link definitions][1]

Or leave it empty and use the [link text itself].

URLs and URLs in angle brackets will automatically get turned into links.
http://www.example.com or <http://www.example.com> and sometimes
example.com (but not on Github, for example).

Here\'s our logo (hover to see the title text):

Inline-style:
![alt text](https://github.com/adam-p/markdown-here/raw/master/src/common/images/icon48.png "Logo Title Text 1")

Reference-style:
![alt text][logo]

[logo]: https://github.com/adam-p/markdown-here/raw/master/src/common/images/icon48.png "Logo Title Text 2"

Inline `code` has `back-ticks around` it.

```
No language indicated, so no syntax highlighting.
But let\'s throw in a <b>tag</b>.
```


| Tables        | Are           | Cool  |
| ------------- |:-------------:| -----:|
| col 3 is      | right-aligned | $1600 |
| col 2 is      | centered      |   $12 |
| zebra stripes | are neat      |    $1 |


Markdown | Less | Pretty
--- | --- | ---
*Still* | `renders` | **nicely**
1 | 2 | 3
');
