import React, { useState }from 'react';
import { Box, Typography, Button, Divider, Stack,
  Stepper,
  Step,
  StepLabel,
  FormControlLabel,
  Alert,
  Grid,
  MenuItem,
  Select,
  Autocomplete,
  Radio, RadioGroup,FormControl, FormLabel,CardContent,
} from '@mui/material';
import { Link } from 'react-router-dom';
import PageContainer from '@src/components/container/PageContainer';
import CustomTextField from '@src/components/forms/theme-elements/CustomTextField';
import CustomFormLabel from '@src/components/forms/theme-elements/CustomFormLabel';
import CustomCheckbox from '@src/components/forms/theme-elements/CustomCheckbox';
import ParentCard from '@src/components/shared/ParentCard';
import Logo from '@src/layouts/full/shared/logo/Logo1';
const steps = ['Account', 'Society', 'Subscription'];

const AuthRegister = () => {
  const [activeStep, setActiveStep] = React.useState(0);
  const [skipped, setSkipped] = React.useState(new Set());

  const isStepOptional = (step:any) => step === '';

  const isStepSkipped = (step:any) => skipped.has(step);

  const handleNext = () => {
    let newSkipped = skipped;
    if (isStepSkipped(activeStep)) {
      newSkipped = new Set(newSkipped.values());
      newSkipped.delete(activeStep);
    }

    setActiveStep((prevActiveStep) => prevActiveStep + 1);
    setSkipped(newSkipped);
  };

  const handleBack = () => {
    setActiveStep((prevActiveStep) => prevActiveStep - 1);
  };

  const handleSkip = () => {
    if (!isStepOptional(activeStep)) {
      // You probably want to guard against something like this,
      // it should never occur unless someone's actively trying to break something.
      throw new Error("You can't skip a step that isn't optional.");
    }

    setActiveStep((prevActiveStep) => prevActiveStep + 1);
    setSkipped((prevSkipped) => {
      const newSkipped = new Set(prevSkipped.values());
      newSkipped.add(activeStep);

      return newSkipped;
    });
  };
  //Register Implimentation starts here
  const [selectedCountry, setSelectedCountry] = useState(null);
  const [selectedState, setSelectedState] = useState(null);
  const [selectedPlan, setSelectedPlan] = useState('1');

  const handleCountryChange = (event, newValue) => {
    setSelectedCountry(newValue);
  };
  //list of countries
  const countryOptions = [
    { id: 1, name: 'India' },
    { id: 2, name: 'US' },
  ];
  const handleStateChange = (event, newValue) => {
    setSelectedState(newValue);
  };
  // Replace this with your list of states
  const stateOptions = [
    { id: 1, name: 'State 1' },
    { id: 2, name: 'State 2' },
  ];

  const handlePlanChange = (event) => {
    setSelectedPlan(event.target.value);
  };
  const topCards = [
    { title: 'Starter', digits: '1000' },
    { title: 'Economy', digits: '2000' },
    { title: 'Premium', digits: '3000'},
    { title: 'Premium Plus', digits: '4000'},
  ];
  // eslint-disable-next-line consistent-return
  const handleSteps = (step: any) => {
    switch (step) {
      case 0:
        return (
          <Box>
            <Grid container spacing={1}>
              <CustomFormLabel htmlFor="Name">Full Name</CustomFormLabel>
              <CustomTextField id="Name" placeholder="Full Name" variant="outlined" fullWidth />
              <CustomFormLabel htmlFor="phone_number">Mobile Number</CustomFormLabel>
              <CustomTextField id="phone_number" placeholder="Mobile Number" type="phone_number" variant="outlined" fullWidth />
              <CustomFormLabel htmlFor="Email">Email Address</CustomFormLabel>
              <CustomTextField id="Email" placeholder="Email Address" type="email" variant="outlined" fullWidth />
            </Grid>
          </Box>
        );
      case 1:
        return (
          <Box>
            <CustomFormLabel htmlFor="society_name">Society Name</CustomFormLabel>
            <CustomTextField placeholder="Society Name" id="society_name" variant="outlined" fullWidth />
            <CustomFormLabel htmlFor="Address">Address</CustomFormLabel>
            <CustomTextField id="Address" placeholder="Address" multiline rows={1} variant="outlined" fullWidth />
            {/* Country, State, City */}
            <Grid container spacing={1}>
              <Grid item xs={12} sm={6}>
                <CustomFormLabel htmlFor="country">Country</CustomFormLabel>
                <Autocomplete
                  id="country"
                  fullWidth
                  options={countryOptions}
                  getOptionLabel={(option) => option.name}
                  value={selectedCountry}
                  onChange={handleCountryChange}
                  renderOption={(props, option) => (
                    <MenuItem component="li" {...props}>
                      {option.name}
                    </MenuItem>
                  )}
                  renderInput={(params) => (
                    <CustomTextField
                      {...params}
                      placeholder="Select"
                      aria-label="Country"
                      autoComplete="off"
                      inputProps={{
                        ...params.inputProps,
                        autoComplete: 'new-password', // disable autocomplete and autofill
                      }}
                    />
                  )}
                />
              </Grid>

              <Grid item xs={12} sm={6}>
                <CustomFormLabel htmlFor="state">State</CustomFormLabel>
                <Autocomplete
                  id="state"
                  fullWidth
                  options={stateOptions}
                  getOptionLabel={(option) => option.name}
                  value={selectedState}
                  onChange={handleStateChange}
                  renderOption={(props, option) => (
                    <MenuItem component="li" {...props}>
                      {option.name}
                    </MenuItem>
                  )}
                  renderInput={(params) => (
                    <CustomTextField
                      {...params}
                      placeholder="Select"
                      aria-label="State"
                      autoComplete="off"
                      inputProps={{
                        ...params.inputProps,
                        autoComplete: 'new-password', // disable autocomplete and autofill
                      }}
                    />
                  )}
                />
              </Grid>

              <Grid item xs={12} sm={6}>
                <CustomFormLabel htmlFor="city">City</CustomFormLabel>
                <CustomTextField placeholder="City" id="city" type="text" variant="outlined" fullWidth />
              </Grid>
              <Grid item xs={12} sm={6}>
                <CustomFormLabel htmlFor="pincode">Pincode</CustomFormLabel>
                <CustomTextField placeholder="Pincode" id="pincode" type="text" variant="outlined" fullWidth />
              </Grid>
            </Grid>
          </Box>
        );
      case 2:
        return (
          <Box pt={4} mb={4}>
            <Grid container spacing={2}>
              {topCards.map((topcard, i) => (
                <Grid item xs={12} sm={4} lg={6} key={i}>
                  <Box textAlign="center" 
                    borderRadius={1} // Set the border radius
                    border="1px solid black" // Set the border color and width
                  >
                    <CardContent>
                      <RadioGroup
                        aria-label={`select-plan-${i}`}
                        name={`select-plan-${i}`}
                        value={selectedPlan}
                        onChange={handlePlanChange}
                      >
                        <FormControlLabel
                          value={`${i + 1}`}
                          control={<Radio/>}
                          label={
                            <>
                              <Typography mt={1} variant="subtitle1" fontWeight={600}>
                                {topcard.title}
                              </Typography>
                              <Typography fontSize={11} variant="subtitle1">
                                {`(\u20B9${topcard.digits} per month)`}
                              </Typography>
                            </>
                          }
                        />
                      </RadioGroup>
                    </CardContent>
                  </Box>
                </Grid>
              ))}
            </Grid>
          </Box>
        );
      default:
        break;
    }
  };

  const handleReset = () => {
    setActiveStep(0);
  };

  return (
    <Grid item paddingX='120px' overflow='hidden'>
      {/* <Logo /> */}
      <Typography variant="h3"
        mb={4}
        fontWeight={600}>MTS - Society Register
      </Typography>
      <Stack>
        <Box width="100%">
          <Stepper activeStep={activeStep}>
            {steps.map((label, index) => {
              const stepProps: { completed?: boolean } = {};
              const labelProps: {
                optional?: React.ReactNode;
              } = {};
              if (isStepOptional(index)) {
                labelProps.optional = <Typography variant="caption">Optional</Typography>;
              }
              if (isStepSkipped(index)) {
                stepProps.completed = false;
              }

              return (
                <Step key={label} {...stepProps}>
                  <StepLabel {...labelProps}>{label}</StepLabel>
                </Step>
              );
            })}
          </Stepper>
          {activeStep === steps.length ? (
            <>
              <Stack spacing={2} mt={3}>
                <Alert severity="success">
                  <Typography variant="h6" p={2} fontWeight={400}>
                    Congratulations!!<br/>
                    Your account is created successfully. please click on login button to proceed further.
                  </Typography>
                </Alert>

                <Box textAlign="right">
                  <Link to='/login'>
                    <Button variant="contained" color="secondary">
                      Login
                    </Button>
                  </Link>
                </Box>
              </Stack>
            </>
          ) : (
            <>
              <Box>{handleSteps(activeStep)}</Box>

              <Box display="flex" flexDirection="row" mt={3}>
                <Button
                  color="inherit"
                  variant="contained"
                  disabled={activeStep === 0}
                  onClick={handleBack}
                  sx={{ mr: 1 }}
                >
                  Back
                </Button>
                <Box flex="1 1 auto" />
                {isStepOptional(activeStep) && (
                  <Button color="inherit" onClick={handleSkip} sx={{ mr: 1 }}>
                    Skip
                  </Button>
                )}

                <Button
                  onClick={handleNext}
                  variant="contained"
                  color={activeStep === steps.length - 1 ? 'primary' : 'secondary'}
                >
                  {activeStep === steps.length - 1 ? 'Submit' : 'Next'}
                </Button>
              </Box>
            </>
          )}
        </Box>
      </Stack>
      <Stack direction="row" spacing={1} mt={3}>
        <Typography color="textSecondary" variant="h6" fontWeight="400">
          Already have an Account?
        </Typography>
        <Typography
          component={Link}
          to="/login"
          fontWeight="500"
          sx={{
            textDecoration: 'none',
            color: 'primary.main',
          }}
        >
          Sign In
        </Typography>
      </Stack>
    </Grid>
  );
};

export default AuthRegister;
