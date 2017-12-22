pragma solidity ^0.4.18;

import './Pebbles.sol';

contract ReadToken is Token {
    mapping (address => uint256) public balances;
    mapping (address => mapping (address => uint256)) public allowed;

    modifier onlyAuthor() {
        require(msg.sender == author);
        _;
    }

    Pebbles public pebbles = Pebbles(0x0);
    address public author;
    uint256 public afterSaleKeysAmount;
    uint256 public totalTokens;
    string public symbol;
    uint256 constant DECIMAL = 10**18;
    uint256 constant CURRENCY_RATE = 4 * DECIMAL / 10;

    struct Book {
        string author;
        string title;
        string description;
        uint256 priceForCrowdSale;
        uint256 priceAfterCrowdSale;
    }

    Book public book;

    struct CrowdSale {
        uint16 endDate;
        uint256 goal;
        uint256 softCap;
        uint256 crowdSaleKeysAmount;
    }

    CrowdSale public crowdSale;

    function ReadToken(
        Pebbles _pebbles,
        address _owner,
        string _author,
        string _description,
        string _title,
        uint256 _priceForCrowdSale,
        uint256 _priceAfterCrowdSale,
        uint256 _goal,
        uint256 _softCap,
        uint256 _crowdSaleKeysAmount,
        uint16 _saleEndDate,
        uint256 _afterSaleKeysAmount,
        string _tokenSymbol
    ) public {
        pebbles = _pebbles;
        author = _owner;
        book = Book(_author, _title, _description, _priceForCrowdSale, _priceAfterCrowdSale);
        crowdSale = CrowdSale(_saleEndDate, _goal, _softCap, _crowdSaleKeysAmount);

        symbol = _tokenSymbol;
        totalTokens = _crowdSaleKeysAmount + _afterSaleKeysAmount;
        balances[author] = totalTokens;
    }

    function totalSupply() public returns (uint256) {
        return totalTokens;
    }

    function transfer(address _to, uint256 _value) public returns (bool success) {
        if (balances[msg.sender] < _value || balances[_to] + _value <= balances[_to]) {
            return false;
        }

        balances[msg.sender] -= _value;
        balances[_to] += _value;
        Transfer(msg.sender, _to, _value);
        return true;
    }

    function transferFrom(address _from, address _to, uint256 _value) public returns (bool success) {
        if (balances[_from] < _value || allowed[_from][msg.sender] < _value || balances[_to] + _value <= balances[_to]) {
            return false;
        }
        allowed[_from][msg.sender] -= _value;
        balances[_from] -= _value;
        balances[_to] += _value;
        Transfer(_from, _to, _value);
        return true;
    }

    function approve(address _spender, uint256 _value) public returns (bool success) {
        allowed[msg.sender][_spender] = _value;
        Approval(msg.sender, _spender, _value);
        return true;
    }

    function allowance(address _owner, address _spender) public constant returns (uint256 remaining) {
        return allowed[_owner][_spender];
    }

    function balanceOf(address _owner) public view returns (uint256 balance) {
        return balances[_owner];
    }

    function setAuthor(address _author) public onlyAuthor {
        author = _author;
    }

    function setName(string _title) public onlyAuthor {
        book.title = _title;
    }

    /**
     * @dev Invest PBLs and gain some shares for the sender themself or for someone else
     * @param _recipient Address of the beneficiary
     * @return Purchased shares
     */
    function buyFor(address _recipient) public returns (uint256 purchasedToken) {
        uint256 allowedPbls = pebbles.allowance(msg.sender, this); // Allowed to spend
        if (allowedPbls > pebbles.balanceOf(msg.sender)) { // if allowed to spend more than balance  of the user
            allowedPbls = pebbles.balanceOf(msg.sender); // set allowed to balance of the user
        }

        uint256 tokens = allowedPbls / book.priceForCrowdSale; // How much tokens user can buy for 100 PBL
        if (tokens > balances[author]) { // if more than total
            tokens = balances[author]; // set to max
        }

        // tokens == 0 tokens - token are not being sold, < means overflow
        if (balances[_recipient] + tokens <= balances[_recipient]) {
            return 0;
        }

        uint256 price = tokens * book.priceForCrowdSale; // Set price of the requested tokens
        if (price < tokens) {
            return 0;
        }

        // Check if PBL from Reader was sent to author
        if (!pebbles.transferFrom(msg.sender, author, price)) {
            return 0;
        }

        // Remove tokens from Author and give it to Reader
        balances[author] -= tokens;
        balances[_recipient] += tokens;
        Purchase(msg.sender, price, _recipient, tokens);
        return tokens;
    }

    /**
     * @dev Invest PBLs and gain some shares for the sender
     * @return Purchased shares
     */
    function buy() public returns (uint256 purchasedToken) {
        return buyFor(msg.sender);
    }

    /**
     * @dev Change price of the book token
     * @return New price
     */
    function changePrice(uint256 _newPrice) public returns(uint256 bookPrice) {
        require(msg.sender == author);
        book.priceForCrowdSale = _newPrice;
        return book.priceForCrowdSale;
    }

    function() public { // no direct deposits!
        revert();
    }

    event Purchase(address indexed sender, uint256 price, address indexed recipient, uint256 tokens);
}
