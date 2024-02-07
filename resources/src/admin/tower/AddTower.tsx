import React, { useState, useEffect } from "react";
import {
    Grid,
    FormControlLabel,
    Button,
    RadioGroup,
    Typography,
    FormGroup,
    Switch,
    Box,
    Paper,
    Alert,
    IconButton,
    AlertTitle,CircularProgress,Checkbox
} from "@mui/material";
import { IconMinus, IconPlus, IconTrash } from '@tabler/icons';
import { useParams, Link, useNavigate } from "react-router-dom";
import PageContainer from "@src/components/container/PageContainer";
import Breadcrumb from "@src/layouts/full/shared/breadcrumb/Breadcrumb";
import CustomTextField from "@src/components/forms/theme-elements/CustomTextField";
import axios from "axios";
import * as yup from "yup";
import { useFormik } from "formik";
import CustomFormLabel from "@src/components/forms/theme-elements/CustomFormLabel";
import ParentCard from "@src/components/shared/ParentCard";
import "react-quill/dist/quill.snow.css";
import ReactQuill from "react-quill";
import AddIcon from '@mui/icons-material/Add';
import RemoveIcon from '@mui/icons-material/Remove';
import DeleteIcon from '@mui/icons-material/Delete';
import { useApiMessages } from '@src/common/Utils'; // Import the utility

const AddTower = () => {
    const { showSuccessMessage, showErrorMessage, renderSuccessMessage, renderErrorMessage } = useApiMessages();
    const [isLoading, setIsLoading] = useState(false);
    const [wingsData, setWingsdata] = useState([]);
    const { id } = useParams(); // Access the 'id' parameter from the URL if it exists
    const navigate = useNavigate();
    const [quillText, setQuillText] = useState("");
    //Site Tokens
    const token = localStorage.getItem("authToken");
    const society_token = localStorage.getItem("societyToken");

    const BCrumb = [
        {
            to: "/admin/tower-list",
            title: "Tower List",
        },
        {
            title: id ? "Edit Tower" : "Add Tower",
        },
    ];

    const validationSchema = yup.object().shape({
        tower_name: yup.string().required("Tower Name is required"),
    });

    const formik = useFormik({
        initialValues: {
            tower_name: "",
            wing_name: [""],
        },
        validationSchema,
        onSubmit: async (values) => {
            try {
                setIsLoading(true);
                // Create a FormData object
                const formData = new FormData();
                // Append form fields to the FormData object
                formData.append("tower_name", values.tower_name);
                formData.append("wings", JSON.stringify(values.wing_name));
                // Determine the API URL based on whether it's an edit or add operation
                const appUrl = import.meta.env.VITE_API_URL;
                let API_URL = appUrl + "/api/add-tower";
                (id) ? formData.append("id", id) : '';
                // Make the API POST request
                const response = await axios.post(API_URL, formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "multipart/form-data",
                        "society_id": `${society_token}`,
                    },
                });
                sessionStorage.setItem("successMessage", response.data.message);
                navigate("/admin/tower-list");
            } catch (error) {
                // Handle errors here
                showErrorMessage(error);
                console.error("Edit spare part error:", error);
            }finally{
                setIsLoading(false);
            }
        },
    });
    
    // Helper function to add a new wing name field
    const addWingName = () => {
        formik.setFieldValue('wing_name', [
        ...formik.values.wing_name,
        ''
        ]);
    };
    // Helper function to remove a wing name field
    const removeWingName = (index) => {
        const updatedWingNames = [...formik.values.wing_name];
        updatedWingNames.splice(index, 1);
        formik.setFieldValue('wing_name', updatedWingNames);
    };

    // Fetch existing data if it's an edit operation
    useEffect(() => {
        if (id) {
            fetchData();
        }
    }, [id]);

    const fetchData = async () => {
        try {
            const appUrl = import.meta.env.VITE_API_URL;
            const API_URL = `${appUrl}/api/show-tower/${id}`;
            const response = await axios.get(API_URL, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "multipart/form-data",
                    "society_id": `${society_token}`,
                },
            });

            const data = response.data.data;
            formik.setValues({
                tower_name: data.tower_name,
                wing_name: [],
            });
            setWingsdata(data.wing);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };
    //Update wings
    const updateWingName = async (wingId, wingName) => {
        try {
            // Create a FormData object
            const formData = new FormData();
            // Append form fields to the FormData object
            formData.append("wings_name", wingName);
            // Determine the API URL based on whether it's an edit or add operation
            const appUrl = import.meta.env.VITE_API_URL;
            let API_URL = appUrl + "/api/edit-wing";
            formData.append("id", wingId);
            // Make the API POST request
            const response = await axios.post(API_URL, formData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "multipart/form-data",
                    "society_id": `${society_token}`,
                },
            });
            showSuccessMessage(response.data.message);
        } catch (error) {
            // Handle errors here
            showErrorMessage(error);
            console.error("Edit spare part error:", error);
        }
    };
    //Delete Wings
    const deleteWingName = async (wingId) => {
        try {
            const formData = new FormData();
            formData.append("id", id);
            const appUrl = import.meta.env.VITE_API_URL;
            const API_URL = appUrl + "/api/delete-wing";
            const response = await axios.post(API_URL, formData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "society_id": `${society_token}`,
                },
            });
            showSuccessMessage(response.data.message);
            fetchData();
            //console.log("Success deleting data:", response.data);
        } catch (error) {
            showErrorMessage(error);
        }
    };

    const handleWingsNameChange = (e, index) => {
        const updatedWingsData = wingsData.map((wing, i) =>
            i === index ? { ...wing, wings_name: e.target.value } : wing
        );
        setWingsdata(updatedWingsData);
    };
    return (
        <>
            <PageContainer
                title="Tower"
                description="This is Tower"
            >
            <Breadcrumb title="" items={BCrumb} />
            {renderSuccessMessage()}
            {renderErrorMessage()}
            <ParentCard
                title={
                    id
                        ? "Edit Tower"
                        : "Add Tower"
                }
            >
                <form onSubmit={formik.handleSubmit}>
                    <Grid container spacing={2}>
                        {/* 1 */}
                        <Grid item xs={7}>
                            <CustomFormLabel
                                htmlFor="tower_name"
                                sx={{ mt: 0 }}
                            >
                                Tower Name <span style={{color:'red'}}>*</span>
                            </CustomFormLabel>
                            <CustomTextField
                                id="tower_name"
                                name="tower_name"
                                placeholder="Tower Name"
                                fullWidth
                                value={formik.values.tower_name}
                                onChange={formik.handleChange}
                                error={
                                    formik.touched.tower_name &&
                                    Boolean(formik.errors.tower_name)
                                }
                                helperText={
                                    formik.touched.tower_name &&
                                    formik.errors.tower_name
                                }
                            />
                        </Grid>        
                       
                        <Grid item xs={12}>
                            <CustomFormLabel htmlFor={`wing_name`} sx={{ mt: 0 }}>
                                Wings
                            </CustomFormLabel>

                            {/* This is for updating the existing one */}
                            <Grid container spacing={1} alignItems="center">
                                {wingsData.map((wings, index) => (
                                    <Grid item xs={4} key={index}>
                                        <Grid container spacing={1} alignItems="center">
                                            <Grid item xs={6} mt={(index === 0) ? 0 : 1}>
                                                <CustomTextField
                                                    id={`wingname_${index}`}
                                                    name={`wingname[${index}]`}
                                                    placeholder="Wing Name"
                                                    fullWidth
                                                    value={wings.wings_name}  // Use wings.wings_name directly
                                                    onChange={(e) => handleWingsNameChange(e, index)}
                                                />
                                            </Grid>
                                            <Grid item xs={6} mt={(index === 0) ? 0 : 1}>
                                                <Button
                                                    variant="outlined"
                                                    color="success"
                                                    onClick={() => updateWingName(wings.id, wings.wings_name)}
                                                    title="Update"
                                                >
                                                    Update
                                                </Button>

                                                <Button
                                                    variant="outlined"
                                                    color="error"
                                                    onClick={() => deleteWingName(wings.id)}
                                                    title="Delete"
                                                    style={{ marginLeft: '4px' }}
                                                >
                                                    <DeleteIcon />
                                                </Button>
                                            </Grid>
                                        </Grid>
                                    </Grid>
                                ))}
                            </Grid>

                            <Grid container spacing={1} alignItems="center">        
                                {/* This one for inserting new one */}
                                {formik.values.wing_name.map((wingName, index) => (
                                    <Grid item xs={8} key={index} mt={1}>
                                        <Grid container spacing={1} alignItems="center">
                                            <Grid item xs={8}>
                                                <CustomTextField
                                                    id={`wing_name_${index}`}
                                                    name={`wing_name[${index}]`}
                                                    placeholder="Wing Name"
                                                    fullWidth
                                                    value={wingName}
                                                    onChange={formik.handleChange}
                                                    error={
                                                        formik.touched.wing_name &&
                                                        formik.touched.wing_name[index] &&
                                                        Boolean(formik.errors.wing_name) &&
                                                        Boolean(formik.errors.wing_name[index])
                                                    }
                                                    helperText={
                                                        formik.touched.wing_name &&
                                                        formik.touched.wing_name[index] &&
                                                        formik.errors.wing_name &&
                                                        formik.errors.wing_name[index]
                                                    }
                                                />
                                            </Grid>
                                            <Grid item xs={2} mt={(index === 0) ? 0 : 1}>
                                                {(index === 0) ? 
                                                    id ? 
                                                    <Button
                                                        variant="outlined"
                                                        color="error"
                                                        onClick={() => removeWingName(index)}
                                                        title="Remove"
                                                    >
                                                        <RemoveIcon />
                                                    </Button> : ''
                                                :
                                                    <Button
                                                        variant="outlined"
                                                        color="error"
                                                        onClick={() => removeWingName(index)}
                                                        title="Remove"
                                                    >
                                                        <RemoveIcon />
                                                    </Button>
                                                }
                                            </Grid>
                                        </Grid>
                                    </Grid>
                                ))}
                            </Grid>
                            <Grid item xs={12} mt={2}>
                                <Button
                                    variant="contained"
                                    color="success"
                                    onClick={addWingName}
                                    title="Add"
                                    style={{float:'right'}}
                                >
                                    <AddIcon />
                                    Add More
                                </Button>  
                            </Grid>                      
                        </Grid>
                        
                        {/* Submit Button */}
                        <Grid item xs={12} mt={5} display={'flex'} alignItems={'center'} justifyContent={'center'}>
                            <Link to="/admin/tower-list">
                                <Button
                                    color="warning"
                                    variant="contained"
                                    style={{ marginRight: "10px" }}
                                >
                                    Back
                                </Button>
                            </Link>
                            <Button
                                variant="contained"
                                color="primary"
                                type="submit"
                                disabled={isLoading}
                            >
                                {id ? "Update" : "Submit"}
                                {isLoading && <CircularProgress size={24} color="inherit" />}
                            </Button>
                        </Grid>
                    </Grid>
                </form>
                </ParentCard>
            </PageContainer>
        </>
    );
};

export default AddTower;
