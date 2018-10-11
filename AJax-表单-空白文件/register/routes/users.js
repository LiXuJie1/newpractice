var express = require('express');
var router = express.Router();
var box=[
      // {email:'',passworld:'',introduce:'',area:''}
]
/* GET users listing. */
router.get('/', function(req, res, next) {
  res.send('respond with a resource');
});
router.post('/show',function(req,res,next){
  
  box.push(req.body);
  console.log(box);
  // console.log(box.body);
  res.json({
    status:true,
   msg:box,
  })
})

module.exports = router;
