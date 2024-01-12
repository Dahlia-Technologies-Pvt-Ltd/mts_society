import React from 'react';
import { Box, Typography, Avatar, Stack, ButtonGroup, Button,  TextField, Grid, IconButton, Snackbar, Alert, AlertTitle, Divider } from '@mui/material';
import { Link } from 'react-router-dom';
import { IconMinus, IconPlus, IconTrash } from '@tabler/icons';
import { useSelector, useDispatch } from '@src/store/Store';
import emptyCart from '@src/assets/images/products/empty-shopping-cart.svg';
import { increment, decrement, addItemToCart } from '@src/store/apps/eCommerce/ECommerceSlice';
import { AppState } from '@src/store/Store';
import AlertCart from '@src/customer/enquiry/AlertCart';
import axios from "axios";

import { Divider,Autocomplete } from '@mui/material';

const CartItems = () => {
  const dispatch = useDispatch();
  // for alert when added something to cart
  const [cartalerts, setCartalert] = React.useState(false);
  const [cartalertMsg, setCartalertMsg] = React.useState('');
  const [showQtyErrorMsg, setShowQtyErrorMsg] = React.useState(false);
  // Get Products
  const response = useSelector((state: AppState) => state.ecommerceReducer.cartItems);
  let Cartproduct = null;
  if (response && response.data) {
    Cartproduct = response.data;
  }

  const Increase = (productId: string) => {
    dispatch(increment(productId));
  };

  const Decrease = (productId: string) => {
    dispatch(decrement(productId));
  };

  const [updatedQuantities, setUpdatedQuantities] = React.useState({}); // Store updated quantities locally

  const handleUpdateQuantity = async (productId, productIdValue) => {
    // Get the updated quantity from the local state
    const newQuantity = productIdValue;
    console.log('cart item quantity value', newQuantity);
    // Dispatch an action to update the quantity
    if (newQuantity !== undefined && newQuantity != '' && newQuantity != '0') {
      try {
        const appUrl = import.meta.env.VITE_API_URL;
        const formData = new FormData();
        formData.append('spare_item_management_id', productId);
        formData.append('quantity', newQuantity);
        const API_URL = appUrl + '/api/add-item';
        const token = sessionStorage.getItem('authToken');
        const response =  await axios.post(API_URL, formData, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        dispatch(addItemToCart(response.data));
        setCartalert(true);
        setCartalertMsg('Item quantity updated successfully.');
        console.log("Update when add item:", response.data);
      } catch (error) {
        console.error("Error when add item:", error);
      }
    }else{
      setShowQtyErrorMsg(true);
      setTimeout(() => {
        setShowQtyErrorMsg(false);
      }, 3000);
    }
  };

  const handleCloseCart = (reason: string) => {
    if (reason === 'clickaway') {
      return;
    }
    setCartalert(false);
  };

  const handleDelete = async (itemToDelete) => {
    try {
      const appUrl = import.meta.env.VITE_API_URL;
      const formData = new FormData();
      formData.append('spare_item_management_id', itemToDelete);
      formData.append('quantity', '0');
      const API_URL = appUrl + '/api/add-item';
      const token = sessionStorage.getItem('authToken');
      const response =  await axios.post(API_URL, formData, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });
      dispatch(addItemToCart(response.data));
      setCartalertMsg('Cart item removed successfully.');
      setCartalert(true);
      //console.log("after add item", response.data);
  } catch (error) {
    console.error("Error when remove item:", error);
  }
  };

  console.log('Cart Item Page',Cartproduct);

  return (
    <Box px={3}>
      {Cartproduct ? (
        <>
          <div style={{ maxHeight: '550px', overflow:'auto', marginBottom:'5px', padding:'2px 2px 2px 2px' }}>
            {Cartproduct.map((product: any, index: number) => (
              <Box key={index}>
                <Grid container md={12}  marginY='30px'>
                <Grid container md={4}>
                  <Avatar
                    src={product.primary_image}
                    alt={product.primary_image}
                    sx={{
                      borderRadius: '10px',
                      height: '120px',
                      width: '120px',
                      marginTop: '10px'
                    }}
                  />
                  </Grid>
                  <Grid container md={8}>
                  <Grid container md={12}>
                  <Grid container md={8}>
                  <Typography variant="subtitle2" fontSize={'14px'} color="textPrimary" fontWeight={600}>
                    {product.material_description}
                    </Typography>{' '}</Grid>
                    <Grid container md={2}></Grid>
                    <Grid container md={2}>
                      <IconButton
                        size="small"
                        sx={{alignItems:'flex-start'}}
                        color="error"
                        title="Remove"
                        onClick={() => handleDelete(product.id)}
                      >
                        <IconTrash size="1rem"/>
                    </IconButton>
                    </Grid>
                    </Grid>
                    <Grid container md={11}>
                    <Typography variant="subtitle2" fontSize={'14px'} color="textPrimary" fontWeight={600}>
                      {product.part_no}
                    </Typography>{' '}</Grid>
                    <Grid container md={11}>
                      <Typography variant="subtitle2" fontSize={'14px'} sx={{marginTop:'3px'}} fontWeight="600">
                      ₹{product.sale_price}
                      </Typography></Grid>

                      <Grid container md={11} mt={1} display='flex' alignItems='center' justifyContent='space-between'>
                      <TextField
                        type="number"
                        value={updatedQuantities[product.id] !== undefined ? updatedQuantities[product.id] : product.quantity}
                        onChange={(e) => {
                          let inputValue = e.target.value.trim(); // Trim leading and trailing whitespaces
                          if (inputValue === '' || !isNaN(parseInt(inputValue, 10))) {
                            setUpdatedQuantities({
                              ...updatedQuantities,
                              [product.id]: inputValue,
                            });
                          }
                        }}
                        style={{
                          width: '120px',
                        }}
                        onKeyDown={(e) => (e.key === 'Backspace' || e.key === '-' || e.key === '+' || (e.key >= '0' && e.key <= '9')) ? null : e.preventDefault()}
                        inputProps={{
                          min: 1,
                        }}
                      />

                        <Button
                          color="warning"
                          variant="contained"
                          onClick={() => handleUpdateQuantity(product.id, updatedQuantities[product.id] !== undefined ? updatedQuantities[product.id] : product.quantity)}
                          sx={{
                            minWidth: '50px',
                            fontSize: '0.8rem',
                            padding: '2px',
                          }}
                        >
                          Update
                        </Button>
                        </Grid>

                    </Grid>
                </Grid>
                <Snackbar
                  open={cartalerts}
                  anchorOrigin={{ vertical: 'top', horizontal: 'right' }}
                  autoHideDuration={1000}
                  onClose={handleCloseCart}
                >
                <Alert severity="success" sx={{ mb: 2 }}>
                  <AlertTitle>{cartalertMsg}</AlertTitle>
                </Alert>
                </Snackbar>
                
                <Snackbar
                  open={showQtyErrorMsg}
                  anchorOrigin={{ vertical: 'top', horizontal: 'right' }}
                  autoHideDuration={1000}
                >
                <Alert severity="error" sx={{ mb: 2 }}>
                  <AlertTitle>Quantity can't be set as blank or zero</AlertTitle>
                </Alert>
                </Snackbar>
                
              <Divider/>
              </Box>
            ))}
          </div>
        </>
      ) : (
        <Box textAlign="center" mb={3}>
          <img src={emptyCart} alt="cart" width="200px" />
          <Typography variant="h5" mb={2}>
            Cart is Empty
          </Typography>
          <Button component={Link} to="/customer/enquiry" variant="contained">
            Go back to Spare Parts
          </Button>
        </Box>
      )}
    </Box>
  );
};

export default CartItems;
