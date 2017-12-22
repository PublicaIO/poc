const assert = require('assert');

const Web3 = require('web3');
const web3 = new Web3();
web3.setProvider('http://34.240.93.135:8545');

const PebbleTokenContract = require('../build/contracts/PebbleToken.json');
const ReadTokenContract = require('../build/contracts/ReadToken.json');

const data = {
    account: '0x76466248636A28F315864aA7865AD727Fb87B7e0',
    pblContract: '0x55648de19836338549130b1af587f16bea46f66b',
    gasPrice: web3.utils.toWei('50', 'gwei')
}

describe('Real Journey', () => {

    it('I should have ether balance', (done) => {
        web3.eth.getBalance(data.account)
        .then((balance) => {
            console.log('ETH Balance', web3.utils.fromWei(balance, 'ether'));
            assert.equal(true, balance > 0);
            done();
        })
        .catch((error) => {
            console.error('Error', error);
            assert.equal(null, error);
            done();
        });
    });

    it('I should have PBL balance', (done) => {
        const PebbleToken = new web3.eth.Contract(PebbleTokenContract.abi, data.pblContract);

        PebbleToken.methods.balanceOf(data.account).call((e, balance) => {
            console.log('PBL Balance', web3.utils.fromWei(balance, 'ether'));
            assert.equal(10, web3.utils.fromWei(balance, 'ether'));
            done();
        });
    });

    it('should unlock account', (done) => {
        web3.eth.personal.unlockAccount(data.account, 'option123')
        .then((success) => {
            console.log('Unlocked:', success);
            done();
        })
        .catch((error) => {
            console.log('Not Unlocked:', error);
            done();
        });
    });

    it('should create Book Contract instance', (done) => {
        const ReadToken = new web3.eth.Contract(ReadTokenContract.abi);
        ReadToken.deploy({
            data: ReadTokenContract.bytecode,
            arguments: [
                data.pblContract,
                data.account,
                'Awesome Author',
                'Awesome Description',
                'Awesome Title',
                1,
                100,
                1512645430155
            ]
        })
        .send({
            from: data.account,
            gas: 5200000,
            gasPrice: data.gasPrice
        })
        .on('error', error => {
            console.log(error);
            assert.equal(null, error);
            done();
        })
        .then(ReadContractInstance => {
            data.book = ReadContractInstance;
            assert.equal(true, typeof ReadContractInstance._address !== 'undefined');
            done();
        });
    });

    it('should approve Reader to spend 1 PBL tokens', (done) => {
        const PebbleToken = new web3.eth.Contract(PebbleTokenContract.abi, data.pblContract);
        PebbleToken.methods.approve('0xCa29118a5878fF9778a548C445e8AeC0B5e11B74', 1*10**18)
        .send({
            from: data.account,
            gas: 5200000,
            gasPrice: data.gasPrice
        }, (error, transactionHash) => {
            assert.equal(null, error);
            assert.equal(true, typeof transactionHash !== 'undefined');
            setTimeout(done, 10000);
        });
    });

    it('should unlock account', (done) => {
        web3.eth.personal.unlockAccount(data.account, 'option123')
        .then((success) => {
            console.log('Unlocked:', success);
            done();
        })
        .catch((error) => {
            console.log('Not Unlocked:', error);
            done();
        });
    });

    it('should allow Reader to buy 100 READ Tokens for 1,000 PBL', (done) => {
        const ReadToken = new web3.eth.Contract(ReadTokenContract.abi, '0xCa29118a5878fF9778a548C445e8AeC0B5e11B74');

        ReadToken.methods.buy().send({
            from: data.account,
            gas: 5200000,
            gasPrice: data.gasPrice
        }, (error, transactionHash) => {
            assert.equal(null, error);
            assert.equal(true, typeof transactionHash !== 'undefined');
            setTimeout(done, 60000);
        });
    });

    it('should change my PBL balance', (done) => {
        const PebbleToken = new web3.eth.Contract(PebbleTokenContract.abi, data.pblContract);

        PebbleToken.methods.balanceOf(data.account).call((error, balance) => {
            console.log('My PBL balance', balance);
            assert.equal(null, error);
            assert.equal(0, balance);
            done();
        });
    });

    it('should change my READ (Book) Token balance to 1', (done) => {
        const ReadToken = new web3.eth.Contract(ReadTokenContract.abi, '0xCa29118a5878fF9778a548C445e8AeC0B5e11B74');

        ReadToken.methods.balanceOf(data.account).call((error, balance) => {
            console.log('My Read Token Balance', balance);
            assert.equal(null, error);
            assert.equal(100, balance);
            done();
        });
    });
});

// CONTRACT: 0xCa29118a5878fF9778a548C445e8AeC0B5e11B74