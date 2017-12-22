// const assert = require('assert');

// const Web3 = require('web3');
// const web3 = new Web3();
// web3.setProvider('http://localhost:8545');

// const PebbleTokenContract = require('../build/contracts/PebbleToken.json');
// const BookTokenContract = require('../build/contracts/ReadToken.json');

// const data = {
//     author: null,
//     reader: null, 
//     pbl: null,
//     book: null
// }

// describe('Pebbles Contract', () => {
//     before((done) => {
//         web3.eth.getAccounts().then(accounts => {
//             data.author = accounts[0];
//             data.reader = accounts[1];
//             done();
//         });
//     });

//     it('should have author and reader set', () => {
//         assert.equal(true, data.author !== null);
//         assert.equal(true, data.reader !== null);
//     });

//     it('should create PBL Contract instance', (done) => {
//         const PebbleToken = new web3.eth.Contract(PebbleTokenContract.abi);
        
//         PebbleToken.deploy({
//             data: PebbleTokenContract.bytecode,
//             arguments: [data.author]
//         })
//         .send({ from: data.author, gas: 6500000 })
//         .on('error', error => {
//             assert.equal(null, error);
//             done();
//         })
//         .then(PBLContractInstance => {
//             data.pbl = PBLContractInstance;
//             assert.equal(true, typeof PBLContractInstance._address !== 'undefined');
//             done();
//         });
//     });

//     it('should have 1st account as founder with 1,000,000,000 PBLs', (done) => {
//         data.pbl.methods.balanceOf(data.author).call((e, balance) => {
//             assert(1000000000, balance);
//             done();
//         });
//     });

//     it('should send 1000 to 2nd account', (done) => {
//         data.pbl.methods.transfer(data.reader, 1000)
//         .send({ from: data.author }, () => {
//             setTimeout(() => {
//                 data.pbl.methods.balanceOf(data.reader).call((e, balance) => {
//                     assert.equal(1000, balance);
//                     done();
//                 });
//             }, 1000);
//         });
//     });

//     it('should have 999,999,000 on the 1st account', (done) => {
//         data.pbl.methods.balanceOf(data.author).call((e, balance) => {
//             assert.equal(999999000, balance);
//             done();
//         });
//     });
// });

// describe('READ (Book) Token Contract', () => {
//     before((done) => {
//         web3.eth.getAccounts().then(accounts => {
//             data.author = accounts[0];
//             data.reader = accounts[1];
//             done();
//         });
//     });

//     it('should have author and reader set', () => {
//         assert.equal(true, data.author !== null);
//         assert.equal(true, data.reader !== null);
//     });

//     it('should create Book Contract instance', (done) => {
//         const BookToken = new web3.eth.Contract(BookTokenContract.abi);
        
//         BookToken.deploy({
//             data: BookTokenContract.bytecode,
//             arguments: [
//                 data.pbl._address,
//                 data.author,
//                 'Awesome Author',
//                 'Awesome Description',
//                 'Awesome Title',
//                 10,
//                 10000,
//                 1512645430155
//             ]
//         })
//         .send({ from: data.author, gas: 6500000 })
//         .on('error', error => {
//             console.log(error);
//             assert.equal(null, error);
//             done();
//         })
//         .then(BookContractInstance => {
//             data.book = BookContractInstance;
//             assert.equal(true, typeof BookContractInstance._address !== 'undefined');
//             done();
//         });
//     });

//     // it('should set book PBL to PBL contract address', (done) => {

//     // });

//     // it('should set book author address to 1st account', (done) => {

//     // });

//     // it('should set book description to "Awesome Description"', (done) => {

//     // });

//     // it('should set book title to "Awesome Title"', (done) => {

//     // });

//     // it('should set book price to 10', (done) => {

//     // });

//     // it('should set book token to 10,000', (done) => {

//     // });

//     // it('should set book timestamp to 1512645430155', (done) => {

//     // });

//     it('should set Reader READ (Book) Token balance to 0', (done) => {
//         data.book.methods.balanceOf(data.reader).call((error, balance) => {
//             assert.equal(null, error);
//             assert.equal(0, balance);
//             done();
//         });
//     });

//     it('should set Author READ (Book) Token balance to 10000', (done) => {
//         data.book.methods.balanceOf(data.author).call((error, balance) => {
//             assert.equal(null, error);
//             assert.equal(10000, balance);
//             done();
//         });
//     });

//     it('should approve Reader to spend 1000 PBL tokens', (done) => {
        // data.pbl.methods.approve(data.book._address, 1000)
        // .send({ from: data.reader }, (error, transactionHash) => {
        //     assert.equal(null, error);
        //     assert.equal(true, typeof transactionHash !== 'undefined');
        //     done();
        // });
//     });

//     it('should allow Reader to buy 100 READ Tokens for 1,000 PBL', (done) => {
        // data.book.methods.buy().send({ from: data.reader }, (error, transactionHash) => {
        //     assert.equal(null, error);
        //     assert.equal(true, typeof transactionHash !== 'undefined');
        //     setTimeout(done, 5000);
        // });
//     });

//     it('should change Reader PBL balance to 0', (done) => {
//         data.pbl.methods.balanceOf(data.reader).call((error, balance) => {
//             assert.equal(null, error);
//             assert.equal(0, balance);
//             done();
//         });
//     });

//     it('should change Author PBL balance to 1,000,000,000', (done) => {
//         data.pbl.methods.balanceOf(data.author).call((error, balance) => {
//             assert.equal(null, error);
//             assert.equal(1000000000, balance);
//             done();
//         });
//     });

//     it('should change Reader READ (Book) Token balance to 100', (done) => {
//         data.book.methods.balanceOf(data.reader).call((error, balance) => {
//             assert.equal(null, error);
//             assert.equal(100, balance);
//             done();
//         });
//     });

//     it('should change Author READ (Book) Token balance to 9,900', (done) => {
//         data.book.methods.balanceOf(data.author).call((error, balance) => {
//             assert.equal(null, error);
//             assert.equal(9900, balance);
//             done();
//         });
//     });
// });