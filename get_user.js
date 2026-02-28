const https = require('https');
const options = {
    hostname: 'api.github.com',
    path: '/user',
    method: 'GET',
    headers: {
        'User-Agent': 'Node.js',
        'Authorization': 'token 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'
    }
};

const req = https.request(options, (res) => {
    let data = '';
    res.on('data', (chunk) => { data += chunk; });
    res.on('end', () => {
        const fs = require('fs');
        fs.writeFileSync('gh_user_node.json', data);
    });
});
req.on('error', (e) => {
    console.error(e);
});
req.end();
