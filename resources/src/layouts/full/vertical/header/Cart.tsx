import React, { useState, useEffect } from 'react';
import { sum } from 'lodash';
import {  IconShoppingCart, IconX } from '@tabler/icons';
import { Box, Typography, Badge, Drawer, IconButton, Button, Stack } from '@mui/material';
import { useSelector, useDispatch } from '@src/store/Store';
import { Link } from 'react-router-dom';
import CartItems from './CartItem';
import { addItemToCart, addProfilePicture } from '@src/store/apps/eCommerce/ECommerceSlice';
import { AppState } from '@src/store/Store';
import axios from "axios";

const Cart = () => {
  // Get Products
  //const Cartproduct = useSelector((state: AppState) => state.ecommerceReducer.cart);
  const Cartproduct = useSelector((state: AppState) => state.ecommerceReducer.cartItems);
  const dispatch = useDispatch();

  useEffect(() => {
    const fetchData = async () => {
      try {
        const appUrl = import.meta.env.VITE_API_URL;
        const API_URL = appUrl + '/api/list-cart-items';
        const token = sessionStorage.getItem('authToken');
        const response = await axios.post(API_URL, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        if (response && response.data && response.data.data) {
          dispatch(addItemToCart(response.data));
          //console.log('useeffect data in cart page',response.data);
        }
      } catch (error) {
        console.error("Error fetching data:", error);
      } 
   }
   if(Cartproduct == null){
   fetchData();
   }
  }, []);


  let totalItems = '0';
  if (Cartproduct && Cartproduct.cart_total) {
  totalItems = Cartproduct.cart_total.total_items;
  //console.log('cart page', totalItems);
}

  const bcount = totalItems;
  //console.log('bcount', bcount);
  //const bcount = Cartproduct.length > 0 ? Cartproduct.length : '0';

  const checkout = useSelector((state: AppState) => state.ecommerceReducer.cart);
  const total = sum(checkout.map((product: any) => product.price * product.qty));

  const [showDrawer, setShowDrawer] = useState(false);
  const handleDrawerClose = () => {
    setShowDrawer(false);
  };

  const cartContent = (
    <Box>
      {/* ------------------------------------------- */}
      {/* Cart Content */}
      {/* ------------------------------------------- */}
      <Box>
        <CartItems />
      </Box>
    </Box>
  );

  return (
    <Box>
      <IconButton
        size="large"
        color="inherit"
        onClick={() => setShowDrawer(true)}
        sx={{
          color: 'text.secondary',
          ...(showDrawer && {
            color: 'primary.main',
          }),
        }}
      >
        <Badge color="error" badgeContent={bcount}>
          <IconShoppingCart size="21" stroke="1.5" />
        </Badge>
      </IconButton>
      {/* ------------------------------------------- */}
      {/* Cart Sidebar */}
      {/* ------------------------------------------- */}
      <Drawer
        anchor="right"
        open={showDrawer}
        onClose={() => setShowDrawer(false)}
        PaperProps={{ sx: { maxWidth: '500px', minWidth:'400px' } }}
      >
        <Box display="flex" alignItems="center" p={3} pb={0} justifyContent="space-between">
          <Typography variant="h5" fontWeight={600}>
            Enquiry Cart
          </Typography>
          <Box>
            <IconButton
              color="inherit"
              sx={{
                color: (theme) => theme.palette.grey.A200,
              }}
              onClick={handleDrawerClose}
            >
              <IconX size="1rem" />
            </IconButton>
          </Box>
        </Box>

        {/* component */}
        {cartContent}
        {/* ------------------------------------------- */}
        {/* Checkout  */}
        {/* ------------------------------------------- */}
        <Box px={3} mt={2}>
          {Cartproduct && Cartproduct.cart_total ? (
            <>
              <Stack direction="row" justifyContent="space-between" mb={3}>
                <Typography variant="subtitle2" fontWeight={400}>
                  Total
                </Typography>
                <Typography variant="subtitle2" fontWeight={600}>
                ₹{Cartproduct.cart_total?.total_amount}
                </Typography>
              </Stack>
              <Button
                fullWidth
                component={Link}
                to="/customer/checkout"
                variant="contained"
                color="primary"
              >
                Checkout
              </Button>
            </>
          ) : (
            ''
          )}
        </Box>
      </Drawer>
    </Box>
  );
};

export default Cart;
